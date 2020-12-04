<?php namespace SwissCollections\RenderConfig;

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
     * @param String[] $groupOrder
     * @param Callable $fieldOrderProvider with $groupName
     * @param Callable $subfieldOrderProvider with $groupName x $fieldName
     */
    public function orderGroups($groupOrder, $fieldOrderProvider, $subfieldOrderProvider) {
        $newGroups = [];
        foreach ($groupOrder as $groupName) {
            $gc = $this->get($groupName);
            if ($gc) {
                $newGroups[] = $gc;
                $gc->orderFields($fieldOrderProvider($groupName), $subfieldOrderProvider);
            }
        }
        foreach ($this->info as $renderGroupConfig) {
            if (!in_array($renderGroupConfig->getName(), $groupOrder)) {
                $newGroups[] = $renderGroupConfig;
                $renderGroupConfig->orderFields($fieldOrderProvider($groupName), $subfieldOrderProvider);
            }
        }
        $this->info = $newGroups;
    }
}