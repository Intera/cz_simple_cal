<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Tests\Unit\Recurrance\Type\Mocks;

use InvalidArgumentException;
use Tx\CzSimpleCal\Domain\Interfaces\IsRecurring;
use Tx\CzSimpleCal\Utility\DateTime;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class IsRecurringMock implements IsRecurring
{
    protected $end = null;

    protected $recurranceSubtype = null;

    protected $recurranceType = null;

    protected $recurranceUntil;

    protected $start = null;

    /**
     * create a new instance with data from a given array
     *
     * @param $data
     * @return $this
     */
    public static function fromArray($data)
    {
        $className = __CLASS__;
        $obj = new $className();

        foreach ($data as $name => $value) {
            $methodName = 'set' . GeneralUtility::underscoredToUpperCamelCase($name);

            // Check if there is a setter defined (use of is_callable to check if the scope is public)
            if (!is_callable([$obj, $methodName])) {
                throw new InvalidArgumentException(
                    sprintf('Could not find the %s method to set %s in %s.', $methodName, $name, get_class($obj))
                );
            }

            call_user_func([$obj, $methodName], $value);
        }

        return $obj;
    }

    /**
     * get the end of this domain model
     *
     * @return DateTime
     */
    public function getDateTimeObjectEnd()
    {
        return new DateTime($this->end);
    }

    public function getDateTimeObjectRecurranceUntil()
    {
        return new DateTime($this->recurranceUntil);
    }

    /**
     * get the start of this domain model
     *
     * @return DateTime
     */
    public function getDateTimeObjectStart()
    {
        return new DateTime($this->start);
    }

    public function getRecurranceSubtype()
    {
        return $this->recurranceSubtype;
    }

    public function getRecurranceType()
    {
        return $this->recurranceType;
    }

    public function getRecurranceUntil()
    {
        return $this->recurranceUntil;
    }

    public function setEnd($end)
    {
        $this->end = $end;
    }

    public function setRecurranceSubtype($recurranceSubtype)
    {
        $this->recurranceSubtype = $recurranceSubtype;
    }

    public function setRecurranceType($recurranceType)
    {
        $this->recurranceType = $recurranceType;
    }

    public function setRecurranceUntil($recurranceUntil)
    {
        $this->recurranceUntil = $recurranceUntil;
    }

    public function setStart($start)
    {
        $this->start = $start;
    }
}
