import SectionLoader from "./SectionLoader";
import SectionResult from "./SectionResult";


/**
 * Data structure that represents a section within the auto-suggest results.
 */
export default class Section {

    /**
     * Label to display in the section header
     */
    readonly label: string;

    /**
     * Determines how many search results have to be requested for this section. If not a number or a negative number
     * the default section limit is applied. When the limit is set to zero, then section will be ignored and no search
     * request will be performed.
     */
    readonly limit?: number;

    /**
     * The position of this section in the search result list container
     */
    position?: number;

    /**
     * The last search results
     */
    result?: SectionResult;

    /**
     * The searcher to use for requesting results in this section
     */
    readonly searcher: string;

    /**
     * The search type to filter results on request for this section
     */
    readonly type: string;

    /**
     * The field to read from search result items for use in item links as query.
     */
    readonly field: "label" | "value";

    /**
     * The last search string queried.
     */
    query?: string;

    /**
     * The section loader used for this section.
     */
    loader?: SectionLoader;
}
