<?php

namespace ECG\Util;

class ArrayReader
{
    /**
     * Read data from subject, or return null.
     *
     * @param array $subject
     * @param string $key
     * @return mixed
     */
    public function readDataOrNull($subject, $key)
    {
        $parts = explode('/', $key);

        foreach ($parts as $part) {
            if (is_array($subject) && isset($subject[$part])) {
                $subject = $subject[$part];
            } else {
                return null;
            }
        }

        return $subject;
    }

    /**
     * Read data from subject or throw exception.
     *
     * @param array $subject
     * @param string $key
     * @throws InvalidArgumentException
     * @return mixed
     */
    //public function readDataOrException(array $subject, $key)
    //{
    //    $result = $this->readDataOrNull($subject, $key);
    //    if (null === $result) {
    //        throw new InvalidArgumentException(sprintf('Key "%s" is not found in subject.', $key));
    //    }
    //    return $result;
    //}
}
