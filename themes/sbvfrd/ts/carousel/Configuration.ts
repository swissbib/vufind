
import ConfigurationItem from "./ConfigurationItem";
import Pagination from "./Pagination";

/**
 * Provides an interface to manage Bootstrap carousel component configurations.
 */
export default interface Configuration {

    /**
     * Adds information for a Bootstrap carousel component rendered on the current page.
     *
     * @param {string} id
     * The unique identifier of the carousel. Each carousel component's root element has an id-attribute value of the
     * format results-carousel-<id> where <id> is the value passed in for this parameter.
     *
     * @param {string} template
     * A template string that contains placeholders for page index and page size to implement pagination.
     *
     * @param {Pagination} pagination
     * Responsive pagination data to handle carousel slides on different screen sizes.
     *
     * @param {string} thumbnail
     * The path to an image to be used as fallback image when a data entry in the carousel does not provide one.
     *
     * @param {number} total
     * The total amount of data entries to show in the carousel.
     *
     * @return {ConfigurationItem}
     * The resulting configuration item entry.
     */
    add: (id: string, template: string, pagination: Pagination, thumbnail: string, total: number) => ConfigurationItem;

    /**
     * Accessor for previously registered carousel configuration.
     *
     * @param {string} id
     * The unique identifier of the carousel to retrieve the configuration for.
     *
     * @returns {ConfigurationItem}
     * The configuration item entry in case the identifier exists or null otherwise.
     */
    get: (id: string) => ConfigurationItem | null;

    /**
     * Provides all available results carousel info identifiers.
     *
     * @return {Array<string>}
     */
    identifiers: () => Array<string>;

    /**
     * Indicates whether there are carousel infos are registered.
     *
     * @returns {boolean}
     */
    available: () => boolean;
}