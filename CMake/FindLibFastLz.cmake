
if (LIBFASTLZ_LIBRARIES AND LIBFASTLZ_INCLUDE_DIRS)
  set (LibFastLz_FIND_QUIETLY TRUE)
endif (LIBFASTLZ_LIBRARIES AND LIBFASTLZ_INCLUDE_DIRS)

find_path(LIBFASTLZ_INCLUDE_DIRS NAMES fastlz.h)
find_library(LIBFASTLZ_LIBRARIES NAMES libfastlz)

include (FindPackageHandleStandardArgs)
FIND_PACKAGE_HANDLE_STANDARD_ARGS(LibPng DEFAULT_MSG
  LIBFASTLZ_LIBRARIES
  LIBFASTLZ_INCLUDE_DIRS)

mark_as_advanced(LIBFASTLZ_INCLUDE_DIRS LIBFASTLZ_LIBRARIES)
