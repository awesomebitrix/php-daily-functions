<?
namespace bfday\PHPDailyFunctions\Helpers\KeyValueCheckers;

class KeyRegexpChecker implements KeyValueCheckerInterface
{
    protected $regExp;
    protected $isRegistryIndependent;

    /**
     * KeyRegexpedChecker constructor.
     *
     * @param      $regExp - no trailing slashes (/blahblah/) is needed
     * @param bool $isFullStringMatch - make full string match regExp?
     * @param bool $isRegistryIndependent - make registry independent regExp?
     *
     * @throws \Exception
     */
    public function __construct($regExp, $isFullStringMatch = true, $isRegistryIndependent = true)
    {
        $this->regExp = '/';
        if ($isFullStringMatch) {
            $this->regExp .= '^';
        }

        $varName = 'regExp';
        if ($$varName === null || !is_string($$varName) || empty($$varName)) {
            throw new \Exception("\$$varName must be not empty string");
        }
        $this->regExp .= $$varName;

        if ($isFullStringMatch) {
            $this->regExp .= '$';
        }
        $this->regExp .= '/';

        $this->isRegistryIndependent = $isRegistryIndependent === true ?: false;
        if ($this->isRegistryIndependent) {
            $this->regExp .= 'i';
        }
    }

    public function run($key, $value)
    {
        if (preg_match($this->regExp, $key)) {
            return true;
        } else {
            return false;
        }
    }
}