<?
namespace bfday\PHPDailyFunctions\Bitrix\Commands\IBlock\Import;

use bfday\PHPDailyFunctions\Bitrix\Helpers\IBlock;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Loader;
use Bitrix\Main\SiteTable;
use Phinx\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Uses 1C-Bitrix (and other APIs of this D7 and oldscool framework) XML import mechanism to import IBlocks from XML files with
 * corresponding media resources which contains in file directory.
 *
 * Note: to use with 1C-Bitrix initiate settings for JEDI using this class like this:
 * new \bfday\PHPDailyFunctions\Bitrix\Commands\IBlock\Import\FromXML()
 *
 * Class FromXML
 * @package classes\Royal\Commands\IBlock\Import
 */
class FromXML extends AbstractCommand
{
    // options
    const OPTION__IMPORT_DIR              = 'importDir';
    const OPTION__IMPORT_DIR_SHORT        = 'i';
    const OPTION__IMPORT_DIR__DESCRIPTION = 'Directory where to seek for imports';

    const OPTION__MODE              = 'mode';
    const OPTION__MODE_SHORT        = 'm';
    const OPTION__MODE__DESCRIPTION = 'Work mode';

    const OPTION__FILES              = 'files';
    const OPTION__FILES_SHORT        = 'f';
    const OPTION__FILES__DESCRIPTION = "Names of files that should be imported. Use semicolon to separate names. Depends on '--mode'";

    const OPTION__FILES_EXCLUDED              = "files-excluded";
    const OPTION__FILES_EXCLUDED_SHORT        = "e";
    const OPTION__FILES_EXCLUDED__DESCRIPTION = "Files that have to be excluded from import. Use semicolon to separate names.";

    const OPTION__SITE_ID              = "site-id";
    const OPTION__SITE_ID_SHORT        = 's';
    const OPTION__SITE_ID__DESCRIPTION = "1C-Bitrix Site identifier.";

    const OPTION__IBLOCK_TYPE              = "iblock-type";
    const OPTION__IBLOCK_TYPE_SHORT        = 'b';
    const OPTION__IBLOCK_TYPE__DESCRIPTION = "1C-Bitrix IBlock type identifier.";

    const OPTION__IMPORT_ACTION__SECTION              = "import-action--section";
    const OPTION__IMPORT_ACTION__SECTION_SHORT        = 'y';
    const OPTION__IMPORT_ACTION__SECTION__DESCRIPTION = "What to do with sections (in DB) which doesn't exists in import file.";

    const OPTION__IMPORT_ACTION__ELEMENT              = "import-action--element";
    const OPTION__IMPORT_ACTION__ELEMENT_SHORT        = 'u';
    const OPTION__IMPORT_ACTION__ELEMENT__DESCRIPTION = "What to do with elements (in DB) which doesn't exists in import file.";

    const OPTION__IMPORT_SUCCESSFULL_ACTION              = "import-successfull-action";
    const OPTION__IMPORT_SUCCESSFULL_ACTION_SHORT        = 'a';
    const OPTION__IMPORT_SUCCESSFULL_ACTION__DESCRIPTION = "What to do when import finished successfully.";
    const IMPORT_SUCCESSFULL_ACTION__DO_NOTHING          = 'N';
    const IMPORT_SUCCESSFULL_ACTION__DO_RENAME           = 'R';
    const IMPORT_SUCCESSFULL_ACTION__DO_DELETE           = 'D';

    // automation
    /*
     * ToDo: automation such detecting IBlock type and site id by filename
    */

    protected function configure()
    {
        $this
            ->setName('import:IBlocksFromXML')
            // template string
            //->addOption(static::template_string1,                     static::template_string1_SHORT,                     InputOption::VALUE_OPTIONAL,    static::template_string1__DESCRIPTION,                  null)
            // VALUE_REQUIRED doesn't work maybe because JEDI
            ->addOption(static::OPTION__IMPORT_DIR,                     static::OPTION__IMPORT_DIR_SHORT,                   InputOption::VALUE_OPTIONAL,    static::OPTION__IMPORT_DIR__DESCRIPTION,                Defaults::IMPORT_URL)
            ->addOption(static::OPTION__MODE,                           static::OPTION__MODE_SHORT,                         InputOption::VALUE_REQUIRED,    static::OPTION__MODE__DESCRIPTION,                      Defaults::WORK_MODE__FILE)
            ->addOption(static::OPTION__FILES,                          static::OPTION__FILES_SHORT,                        InputOption::VALUE_OPTIONAL,    static::OPTION__FILES__DESCRIPTION,                     null)
            ->addOption(static::OPTION__FILES_EXCLUDED,                 static::OPTION__FILES_EXCLUDED_SHORT,               InputOption::VALUE_OPTIONAL,    static::OPTION__FILES_EXCLUDED__DESCRIPTION,            null)
            ->addOption(static::OPTION__SITE_ID,                        static::OPTION__SITE_ID_SHORT,                      InputOption::VALUE_REQUIRED,    static::OPTION__SITE_ID__DESCRIPTION,                   null)
            ->addOption(static::OPTION__IBLOCK_TYPE,                    static::OPTION__IBLOCK_TYPE_SHORT,                  InputOption::VALUE_REQUIRED,    static::OPTION__IBLOCK_TYPE__DESCRIPTION,               null)
            ->addOption(static::OPTION__IMPORT_ACTION__SECTION,         static::OPTION__IMPORT_ACTION__SECTION_SHORT,       InputOption::VALUE_OPTIONAL,    static::OPTION__IMPORT_ACTION__SECTION__DESCRIPTION,    Defaults::IMPORT_ACTION__SECTION__DO_NOTHING)
            ->addOption(static::OPTION__IMPORT_ACTION__ELEMENT,         static::OPTION__IMPORT_ACTION__ELEMENT_SHORT,       InputOption::VALUE_OPTIONAL,    static::OPTION__IMPORT_ACTION__ELEMENT__DESCRIPTION,    Defaults::IMPORT_ACTION__ELEMENT__DO_NOTHING)
            ->addOption(static::OPTION__IMPORT_SUCCESSFULL_ACTION,      static::OPTION__IMPORT_SUCCESSFULL_ACTION_SHORT,    InputOption::VALUE_OPTIONAL,    static::OPTION__IMPORT_SUCCESSFULL_ACTION__DESCRIPTION, static::IMPORT_SUCCESSFULL_ACTION__DO_NOTHING)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $on = 'OPTION__IMPORT_DIR';
        $importDir = $this->checkAndGetOptionValue($input, $on);

        $on = 'OPTION__MODE';
        $mode = $this->checkAndGetOptionValue($input, $on, null, true);

        $on = 'OPTION__FILES';
        $files = explode(Defaults::NAMES_DELIMETER, $this->checkAndGetOptionValue($input, $on));

        $on = 'OPTION__FILES_EXCLUDED';
        $filesExcluded = $this->checkAndGetOptionValue($input, $on);

        $on = 'OPTION__SITE_ID';
        $siteId = $this->checkAndGetOptionValue($input, $on, null, true);
        // check if $siteId exists
        //Loader::includeModule('main');
        $sitesQuery = (new Query(SiteTable::getEntity()));
        if ((new Query(SiteTable::getEntity()))
            ->setSelect([
                'LID'
            ])
            ->setFilter([
                'LID' => $siteId,
            ])
            ->exec()
            ->fetch() === false) {
            throw new \Exception("Site with identifier '{$siteId}' doesn't exists.");
        }

        $on = 'OPTION__IBLOCK_TYPE';
        $iblockType = $this->checkAndGetOptionValue($input, $on, null, true);
        // check if $iblockType exists
        Loader::includeModule('iblock');
        if (!\CIBlockType::GetByID($iblockType)->GetNext()) {
            throw new \Exception("IBlock type with identifier '{$iblockType}' doesn't exists.");
        };

        $on = 'OPTION__IMPORT_ACTION__SECTION';
        $sectionAction = $this->checkAndGetOptionValue($input, $on, [
            Defaults::IMPORT_ACTION__SECTION__DO_NOTHING,
            Defaults::IMPORT_ACTION__SECTION__DO_DEACTIVATE,
            Defaults::IMPORT_ACTION__SECTION__DO_DELETE,
        ]);

        $on = 'OPTION__IMPORT_ACTION__ELEMENT';
        $elementAction = $this->checkAndGetOptionValue($input, $on, [
            Defaults::IMPORT_ACTION__ELEMENT__DO_NOTHING,
            Defaults::IMPORT_ACTION__ELEMENT__DO_DEACTIVATE,
            Defaults::IMPORT_ACTION__ELEMENT__DO_DELETE,
        ]);

        $on = 'OPTION__IMPORT_SUCCESSFULL_ACTION';
        $elementAction = $this->checkAndGetOptionValue($input, $on, [
            static::IMPORT_SUCCESSFULL_ACTION__DO_NOTHING,
            static::IMPORT_SUCCESSFULL_ACTION__DO_RENAME,
            static::IMPORT_SUCCESSFULL_ACTION__DO_DELETE,
        ]);

        switch ($mode) {
            case Defaults::WORK_MODE__FILE:
                $output->writeln('Import started.');
                foreach ($files as $file) {
                    $output->writeln("Importing file ({$file}) in progress...");

                    $fileAbsPath = realpath($_SERVER['DOCUMENT_ROOT'] . $importDir . DIRECTORY_SEPARATOR . $file);

                    if (file_exists($fileAbsPath) && is_file($fileAbsPath)) {
                        $res = ImportXMLFile(
                            $fileAbsPath,
                            $iblockType,
                            $siteId,
                            $sectionAction,
                            $elementAction,
                            $useCRC = true,
                            $preview = false,
                            $sync = false,
                            $returnlastError = true,
                            $returnIblockId = false
                        );

                        if ($res !== true) {
                            $output->writeln("File ({$file}) not imported. Error reported: " . $res);
                        } else {
                            $output->writeln("File ({$file}) successfully imported.");
                        }
                    } else {
                        $output->writeln("File ({$file}) doesnt't exists. Skipped.");
                    }
                }
                break;
            case Defaults::WORK_MODE__DIRECTORY:
                // ToDo: implement later
                break;
            default:
                $on = static::OPTION__MODE;
                throw new \Exception("Not supported option value ({$on} = $mode).");
        }

        $output->writeln("Import finished.");
    }

    /**
     * Checks option value. Returns its value if ok.
     *
     * @param $input InputInterface
     * @param $optionName string
     * @param $supportedValues array|null
     * @param $isRequired bool
     * @return mixed
     * @throws \Exception
     */
    protected function checkAndGetOptionValue(InputInterface $input, $optionName, $supportedValues = null, $isRequired = false) {
        if (!is_string($optionName)) {
            throw new \Exception('$optionName must have string type.');
        }
        if ($supportedValues !== null && !is_array($supportedValues)) {
            throw new \Exception('$supportedValues must have array type or contain null value.');
        }
        $cVal = constant("static::{$optionName}");
        if ($cVal === null) {
            throw new \Exception("Constant with name {$optionName} is not defined in class " . __CLASS__ . ".");
        }
        $v = $input->getOption($cVal);
        if ($isRequired && empty($v)) {
            throw new \Exception("Option value ({$optionName}) can't be empty.");
        }
        if ($supportedValues !== null) {
            if (!in_array($v = $input->getOption($cVal), $supportedValues)) {
                throw new \Exception("Not supported option value ({$optionName} = $v).");
            }
        }
        return $v;
    }
}