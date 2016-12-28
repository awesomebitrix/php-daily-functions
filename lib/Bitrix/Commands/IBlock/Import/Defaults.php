<?
namespace bfday\PHPDailyFunctions\Bitrix\Commands\IBlock\Import;

class Defaults
{
    const NAMES_DELIMETER = ';';

    const IMPORT_ACTION__SECTION__DO_NOTHING = 'N';
    const IMPORT_ACTION__SECTION__DO_DEACTIVATE = 'A';
    const IMPORT_ACTION__SECTION__DO_DELETE = 'D';

    const IMPORT_ACTION__ELEMENT__DO_NOTHING = 'N';
    const IMPORT_ACTION__ELEMENT__DO_DEACTIVATE = 'A';
    const IMPORT_ACTION__ELEMENT__DO_DELETE = 'D';

    const SITE_ID = 's1';

    const IMPORT_URL = '/upload/import';
    const EXPORT_URL = '/upload/export';

    const WORK_MODE__DIRECTORY = 'd';
    const WORK_MODE__FILE = 'f';
}