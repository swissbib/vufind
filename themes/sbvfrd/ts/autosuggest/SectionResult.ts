import Item from "./Item";

/**
 * Represents the search result of a auto-suggest section.
 */
export default interface SectionResult {

    /**
     * An array of search result items.
     */
    items: Item[];

    /**
     * The actual number of search results available
     */
    total: number;
}