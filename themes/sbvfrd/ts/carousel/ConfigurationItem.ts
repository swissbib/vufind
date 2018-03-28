
import Pagination from "./Pagination";

/**
 * An interface that describes a single carousel configuration entry.
 */
export default interface ConfigurationItem {

    /**
     * The unique identifier of the configuration item.
     */
    readonly id: string;

    /**
     * A template string that contains placeholders for page index ('{PAGE-INDEX}') and page size ('{PAGE-SIZE}') to
     * implement pagination.
     */
    readonly templates: ItemTemplates;

    /**
     * The pagination information used handle responsive carousel slide construction.
     */
    readonly pagination: Pagination;

    /**
     * The path to an image to be used as fallback image when a data entry in the carousel does not provide one.
     */
    readonly thumbnail: string;

    /**
     * The amount of data entries to use in total for the carousel.
     */
    readonly total: number;
}


interface ItemTemplates {

    /**
     * Used to fetch new data for the carousel.
     * Placeholders: page, size
     */
    readonly ajax: string;

    /**
     * Used to render the link to the data entry detail page.
     * Placeholders: id
     */
    readonly page: string;

    /**
     * Used to render the info link icon that will open up an inline info container (e.g. knowledge-card).
     * Placeholders: id
     */
    readonly info: string;
}