/**
 * Provides an API to handle pagination data.
 */
export default interface Pagination {

    /**
     * Page size value for extra small screens (e.g. smartphones).
     */
    readonly xs: number;

    /**
     * Page size value for small screens (e.g. tablets).
     */
    readonly sm: number;

    /**
     * Page size value for medium sized screens (e.g. laptops)
     */
    readonly md: number;

    /**
     * Page size value for large screens (e.g. external displays)
     */
    readonly lg: number;
}