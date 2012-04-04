--------------------
mSearch
--------------------
Version: 1.0.0
Author: Vasiliy Naumkin <bezumkin@yandex.ru>
--------------------

You must download dictionaries 
	from http://sourceforge.net/projects/phpmorphy/files/phpmorphy-dictionaries/0.3.x/ru_RU/
	to /core/components/msearch/phpmorphy/dicts/

A simple search snippet with russian morphology support

Feel free to suggest ideas/improvements/bugs on GitHub:
http://github.com/bezumkin/mSearch/issues



Installation
------------------
1. Install package via package management
2. Download only one dictionariy for your language from http://sourceforge.net/projects/phpmorphy/files/phpmorphy-dictionaries/0.3.x/
3. Unpack dictionary to /core/components/msearch/phpmorphy/dicts/
4. Index your resources with running [[!mSearch?&indexer=`1`]] once. In future resources will be indexed when you save it in manager
5. Send $_GET['query'] to page where [[!mSearch]] called. 