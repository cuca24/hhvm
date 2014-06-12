<?php

// This doc comment block generated by idl/sysdoc.php
/**
 * ( excerpt from http://php.net/manual/en/class.splfileobject.php )
 *
 * The SplFileObject class offers an object oriented interface for a file.
 *
 */
class SplFileObject extends SplFileInfo
  implements RecursiveIterator, SeekableIterator {

  const DROP_NEW_LINE = 1;
  const READ_AHEAD = 2;
  const SKIP_EMPTY = 4;
  const READ_CSV = 8;

  private $delimiter = ',';
  private $enclosure = '"';
  private $escape = '\\';
  private $flags;
  private $maxLineLen = 0;
  private $currentLineNum = 0;
  private $rsrc;
  private $currentLine = false;

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.construct.php )
   *
   * Construct a new file object.
   *
   * @filename   mixed   The file to read. TipA URL can be used as a filename
   *                     with this function if the fopen wrappers have been
   *                     enabled. See fopen() for more details on how to
   *                     specify the filename. See the Supported Protocols
   *                     and Wrappers for links to information about what
   *                     abilities the various wrappers have, notes on their
   *                     usage, and information on any predefined variables
   *                     they may provide.
   * @open_mode  mixed   The mode in which to open the file. See fopen() for
   *                     a list of allowed modes.
   * @use_include_path
   *             mixed   Whether to search in the include_path for filename.
   * @context    mixed   A valid context resource created with
   *                     stream_context_create().
   *
   * @return     mixed   No value is returned.
   */
  public function __construct($filename, $open_mode = 'r',
                              $use_include_path = false,
                              $context = null) {
    parent::__construct($filename);
    if (!is_string($open_mode)) {
      throw new Exception(
        'SplFileObject::__construct() expects parameter 2 to be string, '.
        gettype($open_mode).' given'
      );
    }
    $this->rsrc = fopen($filename, $open_mode, $use_include_path, $context);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.eof.php )
   *
   * Determine whether the end of file has been reached
   *
   * @return     mixed   Returns TRUE if file is at EOF, FALSE otherwise.
   */
  public function eof() {
    return feof($this->rsrc);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fflush.php )
   *
   * Forces a write of all buffered output to the file.
   *
   * @return     mixed   Returns TRUE on success or FALSE on failure.
   */
  public function fflush() {
    return fflush($this->rsrc);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fgetc.php )
   *
   * Gets a character from the file.
   *
   * @return     mixed   Returns a string containing a single character read
   *                     from the file or FALSE on EOF. Warning: This
   *                     function may return Boolean FALSE, but may also
   *                     return a non-Boolean value which evaluates to FALSE.
   *                     Please read the section on Booleans for more
   *                     information. Use the === operator for testing the
   *                     return value of this function.
   */
  public function fgetc() {
    return fgetc($this->rsrc);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fgetcsv.php )
   *
   * Gets a line from the file which is in CSV format and returns an array
   * containing the fields read.
   *
   * @delimiter  mixed   The field delimiter (one character only). Defaults
   *                     as a comma or the value set using
   *                     SplFileObject::setCsvControl().
   * @enclosure  mixed   The field enclosure character (one character only).
   *                     Defaults as a double quotation mark or the value set
   *                     using SplFileObject::setCsvControl().
   * @escape     mixed   The escape character (one character only). Defaults
   *                     as a backslash (\) or the value set using
   *                     SplFileObject::setCsvControl().
   *
   * @return     mixed   Returns an indexed array containing the fields read,
   *                     or FALSE on error.
   *
   *                     A blank line in a CSV file will be returned as an
   *                     array comprising a single NULL field unless using
   *                     SplFileObject::SKIP_EMPTY |
   *                     SplFileObject::DROP_NEW_LINE, in which case empty
   *                     lines are skipped.
   */
  public function fgetcsv(
      $delimiter = null,
      $enclosure = null,
      $escape = null) {
    $num_args = func_num_args();
    if ($num_args < 3) {
      $escape = $this->escape;

      if ($num_args < 2) {
        $enclosure = $this->enclosure;

        if ($num_args < 1) {
          $delimiter = $this->delimiter;
        }
      }
    }

    if (!$this->checkCsvControl($delimiter, $enclosure, $escape)) {
      return false;
    }

    return fgetcsv(
      $this->rsrc,
      $this->maxLineLen,
      $delimiter,
      $enclosure,
      $escape,
    );
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fgets.php )
   *
   * Gets a line from the file.
   *
   * @return     mixed   Returns a string containing the next line from the
   *                     file, or FALSE on error.
   */
  public function fgets() {
    $line = fgets($this->rsrc);
    if ($this->flags & self::DROP_NEW_LINE) {
      $line = rtrim($line);
    }
    return $line;
  }

  public function getCurrentLine() {
    return $this->fgets();
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fgetss.php )
   *
   * Identical to SplFileObject::fgets(), except that
   * SplFileObject::fgetss() attempts to strip any HTML and PHP tags from the
   * text it reads.
   *
   * @allowable_tags
   *             mixed   Optional parameter to specify tags which should not
   *                     be stripped.
   *
   * @return     mixed   Returns a string containing the next line of the
   *                     file with HTML and PHP code stripped, or FALSE on
   *                     error.
   */
  public function fgetss($allowable_tags = null) {
    return fgetss($this->rsrc, $this->maxLineLen, $allowable_tags);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.flock.php )
   *
   * Locks or unlocks the file in the same portable way as flock().
   *
   * @operation  mixed   operation is one of the following: LOCK_SH to
   *                     acquire a shared lock (reader). LOCK_EX to acquire
   *                     an exclusive lock (writer). LOCK_UN to release a
   *                     lock (shared or exclusive). LOCK_NB to not block
   *                     while locking (not supported on Windows).
   * @wouldblock mixed   Set to TRUE if the lock would block (EWOULDBLOCK
   *                     errno condition).
   *
   * @return     mixed   Returns TRUE on success or FALSE on failure.
   */
  public function flock($operation, &$wouldblock = false) {
    return flock($this->rsrc, $operation, $wouldblock);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fpassthru.php )
   *
   * Reads to EOF on the given file pointer from the current position and
   * writes the results to the output buffer.
   *
   * You may need to call SplFileObject::rewind() to reset the file pointer
   * to the beginning of the file if you have already written data to the
   * file.
   *
   * @return     mixed   Returns the number of characters read from handle
   *                     and passed through to the output.
   */
  public function fpassthru() {
    return fpassthru($this->rsrc);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fputcsv.php )
   *
   * Writes the fields array to the file as a CSV line.
   *
   * @fields     mixed   An array of values.
   * @delimiter  mixed   The optional delimiter parameter sets the field
   *                     delimiter (one character only).
   * @enclosure  mixed   The optional enclosure parameter sets the field
   *                     enclosure (one character only).
   *
   * @return     mixed   Returns the length of the written string or FALSE on
   *                     failure.
   *
   *                     Returns FALSE, and does not write the CSV line to
   *                     the file, if the delimiter or enclosure parameter is
   *                     not a single character.
   */
  public function fputcsv($fields, $delimiter = null, $enclosure = null) {
    $num_args = func_num_args();
    if ($num_args < 3) {
      $enclosure = $this->enclosure;

      if ($num_args < 2) {
        $delimiter = $this->delimiter;
      }
    }

    if (!$this->checkCsvControl($delimiter, $enclosure)) {
      return false;
    }

    return fputcsv(
      $this->rsrc,
      $fields,
      $delimiter,
      $enclosure
    );
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fscanf.php )
   *
   * Reads a line from the file and interprets it according to the specified
   * format, which is described in the documentation for sprintf().
   *
   * Any whitespace in the format string matches any whitespace in the line
   * from the file. This means that even a tab \t in the format string can
   * match a single space character in the input stream.
   *
   * @format     mixed   The specified format as described in the sprintf()
   *                     documentation.
   *
   * @return     mixed   If only one parameter is passed to this method, the
   *                     values parsed will be returned as an array.
   *                     Otherwise, if optional parameters are passed, the
   *                     function will return the number of assigned values.
   *                     The optional parameters must be passed by reference.
   */
  public function fscanf($format) {
    $argv = array($this->rsrc);
    for ($i = 0; $i < func_num_args(); $i++) {
      $argv[] = func_get_arg($i);
    }
    return call_user_func_array('fscanf', $argv);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fseek.php )
   *
   * Seek to a position in the file measured in bytes from the beginning of
   * the file, obtained by adding offset to the position specified by whence.
   *
   * @offset     mixed   The offset. A negative value can be used to move
   *                     backwards through the file which is useful when
   *                     SEEK_END is used as the whence value.
   * @whence     mixed   whence values are: SEEK_SET - Set position equal to
   *                     offset bytes. SEEK_CUR - Set position to current
   *                     location plus offset. SEEK_END - Set position to
   *                     end-of-file plus offset.
   *
   *                     If whence is not specified, it is assumed to be
   *                     SEEK_SET.
   *
   * @return     mixed   Returns 0 if the seek was successful, -1 otherwise.
   *                     Note that seeking past EOF is not considered an
   *                     error.
   */
  public function fseek($offset, $whence = SEEK_SET) {
    return fseek($this->rsrc, $offset, $whence);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fstat.php )
   *
   * Gathers the statistics of the file. Behaves identically to fstat().
   *
   * @return     mixed   Returns an array with the statistics of the file;
   *                     the format of the array is described in detail on
   *                     the stat() manual page.
   */
  public function fstat() {
    return fstat($this->rsrc);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.ftell.php )
   *
   * Returns the position of the file pointer which represents the current
   * offset in the file stream.
   *
   * @return     mixed   Returns the position of the file pointer as an
   *                     integer, or FALSE on error.
   */
  public function ftell() {
    return ftell($this->rsrc);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.ftruncate.php )
   *
   * Truncates the file to size bytes.
   *
   * @size       mixed   The size to truncate to.
   *
   *                     If size is larger than the file it is extended with
   *                     null bytes.
   *
   *                     If size is smaller than the file, the extra data
   *                     will be lost.
   *
   * @return     mixed   Returns TRUE on success or FALSE on failure.
   */
  public function ftruncate($size) {
    return ftruncate($this->rsrc, $size);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.fwrite.php )
   *
   * Writes the contents of string to the file
   *
   * @str        mixed   The string to be written to the file.
   * @length     mixed   If the length argument is given, writing will stop
   *                     after length bytes have been written or the end of
   *                     string is reached, whichever comes first.
   *
   * @return     mixed   Returns the number of bytes written, or NULL on
   *                     error.
   */
  public function fwrite($str, $length = 0) {
    return fwrite($this->rsrc, $str, $length);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.getchildren.php )
   *
   * An SplFileObject does not have children so this method returns NULL.
   *
   * @return     mixed   No value is returned.
   */
  public function getChildren() {
    return null; // An SplFileOjbect does not have children
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.getcsvcontrol.php
   * )
   *
   * Gets the delimiter and enclosure character used for parsing CSV fields.
   *
   * @return     mixed   Returns an indexed array containing the delimiter
   *                     and enclosure character.
   */
  public function getCsvControl() {
    return array($this->delimiter, $this->enclosure);
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.getflags.php )
   *
   * Gets the flags set for an instance of SplFileObject as an integer.
   *
   * @return     mixed   Returns an integer representing the flags.
   */
  public function getFlags() {
    return $this->flags;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.getmaxlinelen.php
   * )
   *
   * Gets the maximum line length as set by SplFileObject::setMaxLineLen().
   *
   * @return     mixed   Returns the maximum line length if one has been set
   *                     with SplFileObject::setMaxLineLen(), default is 0.
   */
  public function getMaxLineLen() {
    return $this->maxLineLen;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.haschildren.php )
   *
   * An SplFileObject does not have children so this method always return
   * FALSE.
   *
   * @return     mixed   Returns FALSE
   */
  public function hasChildren() {
    return false; // An SplFileOjbect does not have children
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.setcsvcontrol.php
   * )
   *
   * Sets the delimiter and enclosure character for parsing CSV fields.
   *
   * @delimiter  mixed   The field delimiter (one character only).
   * @enclosure  mixed   The field enclosure character (one character only).
   * @escape     mixed   The field escape character (one character only).
   *
   * @return     mixed   No value is returned.
   */
  public function setCsvControl(
      $delimiter = ",",
      $enclosure = "\"",
      $escape = "\\") {
    if (!$this->checkCsvControl($delimiter, $enclosure, $escape)) {
      return;
    }

    $this->delimiter = $delimiter;
    $this->enclosure = $enclosure;
    $this->escape = $escape;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.setflags.php )
   *
   * Sets the flags to be used by the SplFileObject.
   *
   * @flags      mixed   Bit mask of the flags to set. See SplFileObject
   *                     constants for the available flags.
   *
   * @return     mixed   No value is returned.
   */
  public function setFlags($flags) {
    $this->flags = $flags;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.setmaxlinelen.php
   * )
   *
   * Sets the maximum length of a line to be read.
   *
   * @max_len    mixed   The maximum length of a line.
   *
   * @return     mixed   No value is returned.
   */
  public function setMaxLineLen($max_len) {
    if ($max_len < 0) {
      throw new DomainException(
        'Maximum line length must be greater than or equal zero'
      );
    }
    $this->maxLineLen = $max_len;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.current.php )
   *
   * Retrieves the current line of the file.
   *
   * @return     mixed   Retrieves the current line of the file. If the
   *                     SplFileObject::READ_CSV flag is set, this method
   *                     returns an array containing the current line parsed
   *                     as CSV data.
   */
  public function current() {
    if ($this->currentLine === false) {
      if (($this->flags & SplFileObject::READ_CSV) == SplFileObject::READ_CSV) {
        $this->currentLine = $this->fgetcsv(
          $this->delimiter,
          $this->enclosure,
          $this->escape
        );
      } else {
        $this->currentLine = $this->fgets();
      }
    }
    return $this->currentLine;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.key.php )
   *
   * Gets the current line number.
   *
   * This number may not reflect the actual line number in the file if
   * SplFileObject::setMaxLineLen() is used to read fixed lengths of the
   * file.
   *
   * @return     mixed   Returns the current line number.
   */
  public function key() {
    return $this->currentLineNum;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.next.php )
   *
   * Moves ahead to the next line in the file.
   *
   * @return     mixed   No value is returned.
   */
  public function next() {
    $this->currentLine = false;
    $this->currentLineNum++;
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.rewind.php )
   *
   * Rewinds the file back to the first line.
   *
   * @return     mixed   No value is returned.
   */
  public function rewind() {
    rewind($this->rsrc);
    $this->currentLineNum = 0;
    $this->currentLine = false;
    if ($this->flags & self::READ_AHEAD) {
      $this->current();
    }
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.seek.php )
   *
   * Seek to specified line in the file.
   *
   * @line_pos   mixed   The zero-based line number to seek to.
   *
   * @return     mixed   No value is returned.
   */
  public function seek($line_pos) {
    $this->rewind();
    for ($i = 0; $i < $line_pos; $i++) {
      $this->current();
      $this->next();
    }
    $this->current();
  }

  // This doc comment block generated by idl/sysdoc.php
  /**
   * ( excerpt from http://php.net/manual/en/splfileobject.valid.php )
   *
   * Check whether EOF has been reached.
   *
   * @return     mixed   Returns TRUE if not reached EOF, FALSE otherwise.
   */
  public function valid() {
    if ($this->flags & self::READ_AHEAD) {
      return $this->current() !== false;
    }
    return !$this->eof();
  }

  private function checkCsvParameter($value, $name) {
    if (!is_string($value) || strlen($value) != 1) {
      error_log("\nWarning: ".$name.' must be a character');
      return false;
    }
    return true;
  }

  private function checkCsvControl($delimiter, $enclosure, $escape = null) {
    if (!$this->checkCsvParameter($delimiter, 'delimiter') ||
        !$this->checkCsvParameter($enclosure, 'enclosure') ||
        func_num_args() > 2 && !$this->checkCsvParameter($escape, 'escape')) {
      return false;
    }

    return true;
  }
}
