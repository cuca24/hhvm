/*
   +----------------------------------------------------------------------+
   | HipHop for PHP                                                       |
   +----------------------------------------------------------------------+
   | Copyright (c) 2010-present Facebook, Inc. (http://www.facebook.com)  |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
*/
#include "hphp/runtime/vm/verifier/pretty.h"

#include <iostream>
#include <iomanip>
#include <sstream>
#include <stdio.h>

#include <folly/Format.h>

#include "hphp/runtime/vm/repo.h"
#include "hphp/runtime/vm/verifier/util.h"
#include "hphp/runtime/vm/verifier/cfg.h"

namespace HPHP {
namespace Verifier {

void printInstr(const Unit* unit, PC pc) {
  std::cout << "  " << std::setw(4) << (pc - unit->entry()) << ":" <<
               (isCF(pc) ? "C":" ") <<
               (isTF(pc) ? "T":" ") <<
               (isFF(pc) ? "F":" ") <<
               std::setw(3) << instrLen(pc) <<
               " " << instrToString(pc, unit) << std::endl;
}

std::string blockToString(const Block* b, const Graph* /*g*/, const Unit* u) {
  std::stringstream out;
  out << "B" << b->id << ":"
      << u->offsetOf(b->start) <<
         "-" << u->offsetOf(b->last) <<
         " rpo=" << b->rpo_id <<
         " succ=";
  for (BlockPtrRange j = succBlocks(b); !j.empty(); ) {
    const Block* s = j.popFront();
    out << "B" << s->id << (j.empty() ? "" : ",");
  }
  if (b->exn) {
    out << " exn=B" << b->exn->id;
  }
  return out.str();
}

void printFPI(const Func* func) {
  const Unit* unit = func->unit();
  PC bc = unit->entry();
  for (auto& fpi : func->fpitab()) {
    printf("  FPI[%d:%d] fpoff=%d parent=%d fpiDepth=%d\n",
           fpiBase(fpi, bc), fpiPast(fpi, bc), fpi.m_fpOff, fpi.m_parentIndex,
           fpi.m_fpiDepth);
  }
}

void printBlocks(const Func* func, const Graph* g) {
  const Unit* unit = func->unit();
  func->prettyPrint(std::cout);
  printFPI(func);
  for (LinearBlocks i(g->first_linear, 0); !i.empty(); i.popFront()) {
    const Block* b = i.front();
    std::cout << blockToString(b, g, unit) << std::endl;
    for (InstrRange j(b->start, b->end); !j.empty(); ) {
      printInstr(unit, j.popFront());
    }
  }
  std::cout << std::endl;
}

void printGml(const Unit* unit) {
  std::string filename = unit->md5().toString() + ".gml";
  FILE* file = fopen(filename.c_str(), "w");
  if (!file) {
    std::cerr << "Couldn't open GML output file " << filename << std::endl;
    return;
  }
  int nextid = 1;
  fprintf(file, "graph [\n"
                "  hierarchic 1\n"
                "  directed 1\n");
  unit->forEachFunc([&](const Func* func) {
    Arena scratch;
    GraphBuilder builder(scratch, func);
    const Graph* g = builder.build();
    int gid = nextid++;
    fprintf(file, "node [ isGroup 1 id %d ]\n", gid);
    // nodes
    for (LinearBlocks j = linearBlocks(g); !j.empty();) {
      const Block* b = j.popFront();
      std::stringstream strbuf;
      unit->prettyPrint(
        strbuf,
        Unit::PrintOpts().range(
          unit->offsetOf(b->start),
          unit->offsetOf(b->end)));
      std::string code = strbuf.str();
      for (int i = 0, n = code.size(); i < n; ++i) {
        if (code[i] == '"') code[i] = '\'';
      }
      fprintf(file, "  node [ id %d gid %d\n"
                    "    graphics [ type \"roundrectangle\" ]"
                    "    LabelGraphics ["
                    "      anchor \"e\""
                    "      alignment \"left\""
                    "      fontName \"Consolas\"\n"
                    "      text \"%s\"\n"
                    "    ]\n"
                    "  ]\n",
              nextid + b->id, gid, code.c_str());
    }
    // edges
    for (LinearBlocks j = linearBlocks(g); !j.empty();) {
      const Block* b = j.popFront();
      for (BlockPtrRange k = succBlocks(b); !k.empty();) {
        const Block* s = k.popFront();
        fprintf(file, "  edge [ source %d target %d ]\n",
                nextid + b->id, nextid + s->id);
      }
      if (b->exn) {
        fprintf(file, "  edge [ source %d target %d"
                      "    graphics [ style \"dotted\" ]"
                      "  ]\n",
                nextid + b->id, nextid + b->exn->id);
      }
    }
    nextid += g->block_count + 1;
  });
  fprintf(file, "]\n");
  fclose(file);
}

void verify_error(const Unit* unit,
                  const Func* func,
                  bool throws,
                  const char* fmt,
                  ...) {
  char buf[1024];
  va_list args;
  va_start(args, fmt);
  vsnprintf(buf, sizeof buf, fmt, args);
  va_end(args);
  auto out = folly::sformat(
    "Verification Error (unit {}{}{}): {}",
    unit->filepath()->data(),
    func ? " func " : "",
    func ? func->fullName()->data() : "",
    buf
  );
  if (throws) {
    throw std::runtime_error(out);
  }
  fprintf(stderr, "%s", out.c_str());
}

}} // namespace HPHP::VM
