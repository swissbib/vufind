
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
    readonly template: string;

    /**
     * The pagination information used handle responsive carousel slide construction.
     */
    readonly pagination: Pagination;
}