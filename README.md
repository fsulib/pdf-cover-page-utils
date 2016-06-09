# pdf-cover-page-utils
Utilities for handling PDF cover pages

# Intro
The PDFs in the DigiNole Research Repository have fancy cover pages, but generating
and updating these can be a royal PITA. If someone's name is incorrect in the metadata,
not only does the MODS record have to be updated, but the PDF's cover page has
to be updated as well. This requires stripping off the first page, generating
a new cover page from the updated metadata, and then merging that new cover page
back into the old uncovered document. This can be very time consuming, but this
utility kit makes it *slightly* less so.

# Dependencies
**pdf-cover-page-utils** requires [PHP](http://php.net/) and [GhostScript](http://www.ghostscript.com) to be installed on your system 
(I've tested using PHP v5.5 and GhostScript v9.16, but other versions *should* be fine).

It also includes the FPDF v1.8.1 and FPDI v1.6.1 PHP libraries, but these are included
in the /assets folder and already set up as a core part of the system, so like don't even
worry about that. They both have permissive licenses so this is probably okay.

# Usage
All of the utility scripts are located in the /bin directory. Put the 
path/to/pdf-cover-page-utils/bin in your [$PATH variable](https://kb.iu.edu/d/acar)
so that you can use this commands directly.

## cover-update
The top level utility in this kit is **cover-update**, which is a wrapper for the
other included utilities. It takes two arguments, with arg 1 being the path to the
PDF that you want to replace the cover page on, and arg 2 being the MODS record
you want to pull the metadata from.
`cover-update path/to/file.pdf path/to/mods.xml`
If you run this command on `file.pdf`, this script will create a new file called
`file.recovered.pdf` in the same directory that has a newly generated cover page.

### Testing
If you want to do a quick test of this tool, use the documents included in
/test-files. /test-files/test-document.pdf has misspelled 

## pdf-split
**pdf-split** is a utility for splitting a PDF out into separate files for the
original cover page and the remainder of the document. It takes 3 arguments: arg 1
is the file you want to split, arg 2 is the name of the output file with the first 
page of the file in arg 1, and arg 3 is the remainder of the pages (everything but
the cover) from the file in arg 1.
`pdf-split path/to/filetosplit.pdf path/to/cover.pdf path/to/remainder.pdf`

## cover-generate
**cover-generate** is a utility for generating a single page PDF cover page from
a MODS record using a template. It takes 2 arguments: arg 1 is the MODS record with
the metadata to be used, and arg 2 is the name of the output file.
`cover-generate path/to/mods.xml path/to/newcover.pdf`

## pdf-merge
**pdf-merge** is a utility for joining 2 PDFs into one. It takes 3 arguments, and
the order is important. Arg 1 is the first PDF file to merge (typically a newly
generated cover page), and will appear first in the new PDF. Arg 2 is the second
file to merge (typically the remainder of a previously uncovered PDF), and will 
appear second in the new PDF. Arg 3 is the name of the combined output PDF.
`pdf-merge path/to/newcover.pdf path/to/remainder.pdf path/to/combined.pdf`

# Modifications
The template used by this tool is set up specifically for Florida State University,
but if you want to fork this tool and add your own template, well then rock on.
The template is in /assets/coverpage.pdf, which is just a blank PDF of FSU letterhead.
Replace it with your own university letterhead, but be aware that you may have to adjust
the coordinates used in /assets/coverpage-generator.php to customize where the text
shows up relative to any pre-existing images in your template.
