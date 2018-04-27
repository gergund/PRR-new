<?php

namespace ECG\Infos;

interface InformationInterface
{
    /**
     * Get information name.
     * This value is used as title/description of current onformation dataset.
     *
     * @return string
     */
    public function getName();

    /**
     * Get information data in form of key-value array.
     *
     * @return array
     */
    public function getData();
}
