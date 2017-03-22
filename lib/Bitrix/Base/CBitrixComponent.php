<?

namespace bfday\PHPDailyFunctions\Bitrix\Base;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Base class for component.
 *
 * Basic PARAMS for ajax mode:
 * [
 *     "AJAX_MODE" => "Y",
 *     "AJAX_OPTION_JUMP" => "N",
 *     "AJAX_OPTION_HISTORY" => "N",
 *     "AJAX_OPTION_ADDITIONAL" => "statistics",
 * ],
 * by default "AJAX_PARAM_NAME" => "compid", but you can change it (but why?)
 *
 * Class CBitrixComponent
 * @package bfday\PHPDailyFunctions\Bitrix\Base
 */
abstract class CBitrixComponent extends \CBitrixComponent
{
    const AJAX__COMPONENT_PARAM_NAME = "compid";

    const AJAX__COMPONENT_CONTAINER__PREFIX = "comp_";

    /**
     * @var array - default values for $arParams var
     */
    protected $arParamsDefaults = array();

    /**
     * @var array - The codes of modules that will be included before executing component
     */
    protected $requiredModules = array();

    /**
     * @var array - Additional cache ID
     */
    private $cacheAdditionalId;

    /**
     * @var string - Cache dir
     */
    protected $cacheDir = false;

    /**
     * @var bool - Caching template of the component (default not cache)
     */
    protected $cacheTemplate = true;

    /**
     * @var string - Salt for component ID for AJAX request
     */
    protected $ajaxComponentIdSalt;

    /**
     * @var null|string - result of ajax work
     */
    protected $ajaxResult = null;

    /**
     * @var string - Template page name
     */
    protected $templatePage;

    /**
     * @var bool - disables to include component template
     */
    private $disableComponentTemplate = false;

    const SEPARATE_CACHE_FOR_EVERY_USER_FOLDER_PREFIX = "user";
    /**
     * Creates additional cache folder for every user.
     * Uses SEPARATE_CACHE_FOR_EVERY_USER_FOLDER_PREFIX to set prefix to that folder.
     */
    private $isSeparateCacheForEveryUser = false;

    /**
     * Do drop tagged cache which applies inside [executeMain]?
     * @var bool
     */
    private $isDropTaggedCache = false;

    /**
     * Array of TaggedCache tags.
     *
     * @var array
     */
    private $taggedCacheTags = [];

    private $isShowExceptionMsgToUser = false;

    final protected function executeBase()
    {
        $this->init();
        $this->includeModules();
        $this->startAjax();
        $this->executeProlog();

        if ($this->startCache()) {
            $this->executeMain();

            if (defined("BX_COMP_MANAGED_CACHE")) {
                if (empty($this->taggedCacheTags)) {
                    if ($this->isDropTaggedCache) {
                        Main\Application::getInstance()->getTaggedCache()->abortTagCache();
                    }
                } else {
                    $taggedCache = Main\Application::getInstance()->getTaggedCache();
                    if ($this->isDropTaggedCache) {
                        $taggedCache->abortTagCache();
                        $taggedCache->startTagCache($this->getCachePath());
                    }
                    foreach ($this->taggedCacheTags as $taggedCacheTag) {
                        $taggedCache->registerTag($taggedCacheTag);
                    }
                    if ($this->isDropTaggedCache) {
                        $taggedCache->endTagCache();
                    }
                }
            }

            if ($this->cacheTemplate) {
                $this->showResult();
            }

            $this->writeCache();
        }

        if (!$this->cacheTemplate) {
            $this->showResult();
        }
        
        $this->executeEpilog();
        $this->stopAjax();
    }

    /**
     * Standard component execution function
     *
     * @return array - result
     */
    public function executeComponent()
    {
        try {
            $this->executeBase();
        } catch (\Exception $e) {
            $this->catchException($e);
        }
        return $this->arResult;
    }

    /**
     * Includes required modules
     *
     * @uses $this->requiredModules
     * @throws \Bitrix\Main\LoaderException
     */
    public function includeModules()
    {
        if (empty($this->requiredModules)) {
            return false;
        }

        foreach ($this->requiredModules as $module) {
            if (!Main\Loader::includeModule($module)) {
                throw new Main\LoaderException(Loc::getMessage("NIK_FAILED_INCLUDE_MODULE", ["#MODULE#" => $module]));
            }
        }

        return true;
    }

    /**
     * Init method. Executes before cache.
     * Use it if you need $this->arParams available but still not in cache section
     *
     * @return bool
     */
    protected function init()
    {
        if (empty($this->arParams["AJAX_PARAM_NAME"])) {
            $this->arParams["AJAX_PARAM_NAME"] = static::AJAX__COMPONENT_PARAM_NAME;
        }
        // init default values for arParams if corresponding values are empty
        $this->arParams = array_replace_recursive($this->arParamsDefaults, $this->arParams);
        return true;
    }

    /**
     * Init caching
     *
     * @return bool
     */
    public function startCache()
    {
        global $USER, $CACHE_MANAGER;

        if ($this->arParams["CACHE_TYPE"] && $this->arParams["CACHE_TYPE"] !== "N" && $this->arParams["CACHE_TIME"] > 0) {
            if ($this->templatePage) {
                $this->cacheAdditionalId[] = $this->templatePage;
            }

            if ($this->arParams["CACHE_GROUPS"] === "Y") {
                $this->cacheAdditionalId[] = $USER->GetGroups();
            }

            // logic for separating caches of users from each other
            if ($this->isSeparateCacheForEveryUser) {
                $dir = DIRECTORY_SEPARATOR . static::SEPARATE_CACHE_FOR_EVERY_USER_FOLDER_PREFIX;
                if ($USER->IsAuthorized()) {
                    $dir .= $USER->GetID();
                }
                if ($this->cacheDir === false) {
                    $this->cacheDir = $CACHE_MANAGER->GetCompCachePath($this->getRelativePath()) . $dir;
                } else {
                    $this->cacheDir .= $dir;
                }
                $this->addCacheAdditionalId($dir);
            }

            if ($this->startResultCache($this->arParams["CACHE_TIME"], $this->cacheAdditionalId, $this->cacheDir)) {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Restarts buffer if is AJAX request
     */
    private function startAjax()
    {
        if (strlen($this->arParams["AJAX_COMPONENT_ID"]) <= 0) {
            $this->arParams["AJAX_COMPONENT_ID"] = \CAjax::GetComponentID($this->getName(), $this->getTemplateName(), $this->ajaxComponentIdSalt);
        }

        if ($this->isAjax()) {
            global $APPLICATION;

            // separate cache for ajax mode
            $this->arParams["IS_IN_AJAX_MODE"] = "Y";

            if ($this->arParams["AJAX_HEAD_RELOAD"] === "Y") {
                $APPLICATION->ShowAjaxHead();
            } else {
                $APPLICATION->RestartBuffer();
            }

            if ($this->arParams["AJAX_TYPE"] === "JSON") {
                header("Content-Type: application/json");
            }

            if (strlen($this->arParams["AJAX_TEMPLATE_PAGE"]) > 0) {
                $this->templatePage = basename($this->arParams["AJAX_TEMPLATE_PAGE"]);
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * Writes cache to disk
     */
    public function writeCache()
    {
        $this->endResultCache();
    }

    /**
     * Resets the cache
     */
    public function abortCache()
    {
        $this->abortResultCache();
    }

    /**
     * Executes before getting results. Always not cached
     */
    protected function executeProlog() {}
    
    /**
     * A method for extending the results of the child classes.
     * The result of this method will be cached
     */
    protected function executeMain() {}

    /**
     * Execute after getting results. Always not cached
     */
    protected function executeEpilog() {}

    /**
     * Stop execute script if in AJAX request
     */
    private function stopAjax()
    {
        if ($this->isAjax()) {
            exit;
        }
    }

    /**
     * Show results. Default: include template of the component
     *
     * @uses $this->templatePage
     */
    public function showResult()
    {
        if (!$this->disableComponentTemplate) {
            $this->includeComponentTemplate($this->templatePage);
        }
    }

    /**
     * Set status 404 and throw exception
     *
     * @internal param bool $notifier Sent notify to admin email
     * @internal param \Exception|false|null $exception Exception which will be throwing or "false" what not throwing exceptions. Default — throw new \Exception()
     */
    public function return404()
    {
        @define("ERROR_404", "Y");
        \CHTTP::SetStatus("404 Not Found");
    }

    /**
     * Called when an error occurs
     * Resets the cache, show error message (two mode: for users and for admins),
     * sending notification to admin email
     *
     * @param \Exception $exception
     */
    protected function catchException(\Exception $exception)
    {
        $this->abortCache();
        if ($GLOBALS["USER"]->IsAdmin()) {
            $this->showExceptionAdmin($exception);
        } else {
            if ($this->isShowExceptionMsgToUser) {
                $this->showExceptionUser($exception);
            }
        }
    }

    /**
     * Display of the error for user
     *
     * @param \Exception $exception
     */
    protected function showExceptionUser(\Exception $exception)
    {
        ShowError($exception->getMessage());
    }

    /**
     * Display of the error for admin
     *
     * @param \Exception $exception
     */
    protected function showExceptionAdmin(\Exception $exception)
    {
        ShowError($exception->getMessage());
        ShowError(nl2br($exception));
    }

    /**
     * Adds (pushes element to array) additional ID to cache
     *
     * @param mixed $id
     */
    public function addCacheAdditionalId($id)
    {
        $this->cacheAdditionalId[] = $id;
    }

    public static function formatDisplayDate($date, $format)
    {
        if (empty($date)) {
            return '';
        }
        return \CIBlockFormatProperties::DateFormat($format, MakeTimeStamp($date, \CSite::GetDateFormat()));
    }

    /**
     * @param boolean $val
     */
    public function setDisableComponentTemplate($val = true)
    {
        if ($val === false)
            $this->disableComponentTemplate = false;
        $this->disableComponentTemplate = true;
    }

    /**
     * Use it before [$this->startCache], for example in [onPrepareComponentParams].
     *
     * @param boolean $val
     */
    public function setIsSeparateCacheForEveryUser($val = true)
    {
        $this->isSeparateCacheForEveryUser = $val;
    }

    /**
     * @param boolean $val
     */
    public function setIsDropTaggedCache($val = true)
    {
        $this->isDropTaggedCache = $val;
    }

    /**
     * @param $tag string
     * @param bool $isDropOtherTags
     */
    public function addTaggedCacheTag($tag, $isDropOtherTags = false)
    {
        if ($isDropOtherTags) $this->setIsDropTaggedCache();
        if (!in_array($tag, $this->taggedCacheTags)) {
            $this->taggedCacheTags[] = $tag;
        }
    }

    /**
     * Show or not exception messages to regular user.
     * @param bool $val
     */
    public function setIsShowExceptionMsgToUser($val = true)
    {
        $this->isShowExceptionMsgToUser = $val;
    }

    // AJAX functionality

    public function isAjax()
    {
        return $_REQUEST[$this->getAjaxParamNameForHttpQuery()] == $this->getAjaxId();
    }

    /**
     * Executes initial procedures to start ajax section
     *
     * @return bool - true if successfully started
     */
    public function ajaxSectionBegin()
    {
        if ($this->isAjax()) {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            return true;
        }
        return false;
    }

    /**
     * Executes final procedures to stop ajax section. Returns true on success.
     * Use it in template (to markup ajax section) like this:
     * if ($component->ajaxSectionEnd()) return;
     *
     * @param $callback - callable, executes before terminating procedures and only if ajax call to current component
     *
     * @return bool
     */
    public function ajaxSectionEnd($callback = null)
    {
        if ($this->isAjax()) {
            if ($callback !== null && is_callable($callback)) {
                call_user_func($callback);
            }
            return true;
        }
        return false;
    }

    public function getAjaxId()
    {
        if (empty($this->arParams["AJAX_ID"])) {
            throw new \Exception('arParams["AJAX_ID"] should be filled. Maybe you call this function in a wrong place or didnt start Ajax mode');
        }
        return $this->arParams["AJAX_ID"];
    }

    public function getAjaxContainerId()
    {
        return static::AJAX__COMPONENT_CONTAINER__PREFIX . $this->getAjaxId();
    }

    public function getAjaxParamNameForHttpQuery()
    {
        if (empty($this->arParams["AJAX_PARAM_NAME"])) {
            throw new \Exception('arParams["AJAX_PARAM_NAME"] should be filled. Maybe you call this function in a wrong place or didnt start Ajax mode');
        }
        return $this->arParams["AJAX_PARAM_NAME"];
    }

    public function getAjaxParamAndValueForHttpQuery()
    {
        return $this->getAjaxParamNameForHttpQuery() . "=" . $this->getAjaxId();
    }
}