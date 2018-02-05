
import ConfigurationItem from "./ConfigurationItem";

/**
 * An interface to load data for a specific carousel over an AJAX request.
 */
export default class DataLoader {

    private item: ConfigurationItem;

    /**
     * Constructor.
     *
     * @param {ConfigurationItem} item
     * The carousel configuration the loader operates on.
     */
    constructor(item: ConfigurationItem) {
        this.item = item;
    }
}