<?
namespace bfday\PHPDailyFunctions\Helpers\KeyValueCheckers;

class KeyPrefixedPostfixedChecker implements KeyValueCheckerInterface
{
    protected $prefix;
    protected $postfix;

    protected $regExp;
    protected $isRegistryIndependent;

    public function __construct($prefix = null, $postfix = null, $isFullStringMatch = true, $isRegistryIndependent = true)
    {
        $this->regExp = '/';
        if ($isFullStringMatch) {
            $this->regExp .= '^';
        }

        $varName = 'prefix';
        if ($$varName !== null && !empty($$varName)) {
            if (!is_string($$varName)) {
                throw new \Exception("\$$varName must be not empty string or null value");
            }
            $this->$$varName = $$varName;
            // add postfix if available
            if ($this->$$varName !== null) {
                $this->regExp .= $$varName;
            }
        }
        // add "any char" expression
        $this->regExp .= '.*';

        $varName = 'postfix';
        if ($$varName !== null) {
            if (!is_string($$varName) || empty($$varName)) {
                throw new \Exception("\$$varName must be not empty string or null value");
            }
            $this->$$varName = $$varName;
            // add postfix with lazy match if available
            if ($this->$$varName !== null) {
                $this->regExp .= '?' . $$varName;
            }
        }
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