<?php namespace SwissCollections\RenderConfig;

use SwissCollections\RecordDriver\ViewFieldInfo;

class RenderConfig {
    /**
     * @var RenderGroupConfig[]
     */
    protected $info = [];

    public function add(RenderGroupConfig $entry) {
        $this->info[] = $entry;
    }

    /**
     * @return RenderGroupConfig[]
     */
    public function entries() {
        return $this->info;
    }

    public function __toString() {
        $s = "RenderConfig{[\n";
        foreach ($this->info as $e) {
            $s = $s . "\t" . $e . ",\n";
        }
        return $s . "]}";
    }

    /**
     * @param String $groupName
     * @return null|RenderGroupConfig
     */
    public function get(String $groupName) {
        foreach ($this->info as $renderGroupConfig) {
            if ($groupName === $renderGroupConfig->getName()) {
                return $renderGroupConfig;
            }
        }
        return null;
    }

    /**
     * @param ViewFieldInfo $viewFieldInfo
     */
    public function orderGroups($viewFieldInfo) {
        $newGroups = [];
        $groupOrder = $viewFieldInfo->groupNames();
        foreach ($groupOrder as $groupName) {
            $gc = $this->get($groupName);
            if ($gc) {
                $newGroups[] = $gc;
                $gc->orderFields($viewFieldInfo);
            }
        }
        foreach ($this->info as $renderGroupConfig) {
            if (!in_array($renderGroupConfig->getName(), $groupOrder)) {
                $newGroups[] = $renderGroupConfig;
                $renderGroupConfig->orderFields($viewFieldInfo);
            }
        }
        $this->info = $newGroups;
    }
}