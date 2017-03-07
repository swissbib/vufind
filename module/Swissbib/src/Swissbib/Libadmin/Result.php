<?php
/**
 * Libadmin Result
 *
 * PHP version 5
 *
 * Copyright (C) project swissbib, University Library Basel, Switzerland
 * http://www.swissbib.org  / http://www.swissbib.ch / http://www.ub.unibas.ch
 *
 * Date: 1/2/13
 * Time: 4:09 PM
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category Swissbib_VuFind2
 * @package  Libadmin
 * @author   Guenter Hipler  <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://www.swissbib.org
 */
namespace Swissbib\Libadmin;

/**
 * Synchronization result with messages and status flag
 *
 * @category Swissbib_VuFind2
 * @package  Libadmin
 * @author   Guenter Hipler <guenter.hipler@unibas.ch>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/vufind2:developer_manual Wiki
 */
class Result
{
    /**
     * Result types
     */
    const SUCCESS = 1;
    const INFO = 2;
    const ERROR = 3;

    /**
     * Type labels
     *
     * @var Array
     */
    protected $labels = [
        1 => 'Success',
        2 => 'Info',
        3 => 'Error'
    ];

    /**
     * Was sync successful?
     *
     * @var Bool
     */
    protected $success = true;

    /**
     * Messages
     *
     * @var Array
     */
    protected $messages = [];

    /**
     * Reset result
     *
     * @return void
     */
    public function reset()
    {
        $this->messages = [];
        $this->success  = true;
    }

    /**
     * Add a new message
     *
     * @param Integer $type    Type
     * @param String  $message Message
     *
     * @return void
     */
    public function addMessage($type, $message)
    {
        $this->messages[] = [
            'type'    => (int)$type,
            'message' => $message
        ];
    }

    /**
     * Add an error
     *
     * @param String $message Message
     *
     * @return Result
     */
    public function addError($message)
    {
        $this->addMessage(self::ERROR, $message);

        $this->success = false;

        return $this;
    }

    /**
     * Add an info
     *
     * @param String $message Message
     *
     * @return Result
     */
    public function addInfo($message)
    {
        $this->addMessage(self::INFO, $message);

        return $this;
    }

    /**
     * Add a success
     *
     * @param String $message Message
     *
     * @return Result
     */
    public function addSuccess($message)
    {
        $this->addMessage(self::SUCCESS, $message);

        return $this;
    }

    /**
     * Check whether import was successful
     *
     * @return Boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Check whether import had errors
     *
     * @return Boolean
     */
    public function hasErrors()
    {
        return !$this->success;
    }

    /**
     * Get all plain messages
     *
     * @return Array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get list of formatted (prefixed with status) messages
     *
     * @return String[]
     */
    public function getFormattedMessages()
    {
        $messages = [];

        foreach ($this->messages as $message) {
            $messages[] = $this->labels[$message['type']] . ': ' .
                $message['message'];
        }

        return $messages;
    }
}
