import Item from "./Item";

/**
 * Represents the search result of a auto-suggest section.
 */
export default interface SectionResult {

    /**
     * An array of search result items.
     */
    items: Array<Item>;

    /**
     * The actual number of search results available
     */
    total: number;
}