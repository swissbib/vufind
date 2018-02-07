import Pagination from "./Pagination";
import BootstrapBreakpoints from "../common/BootstrapBreakpoints";

/**
 * Holds the pagination state of a carousel and provides
 */
export default class Paginator {

    /**
     * Constructor.
     *
     * @param {Pagination} pagination
     * The pagination information to calculate new paging values from.
     *
     * @param {number} elementCount
     * The total number of elements the paginator has to take into account for paging calculations.
     */
    // TODO: check whether we actually need elementCount and of so, whether to retrieve it from server-side config or by special request.
    constructor(readonly pagination:Pagination, public elementCount: number = 100) { }

    /**
     * Recalculates all values of the paginator based on the given query.
     *
     * @param {String} query
     */
    public updateFromQuery(query: string): void {
        console.log("Paginator: update from query", query);
        const name = BootstrapBreakpoints.getName(query);
        const newPageSize = Object(this.pagination)[name];

        this._page = Math.floor((this._page * this._size) / newPageSize);
        this._size = newPageSize;
    }

    /**
     * Storage for the page property.
     *
     * @private
     * @type {number}
     */
    private _page: number = 0;

    /**
     * The current page to show carousel content for. It can be changed by {@link #next}, {@link #previous} directly and
     * by {@link #updateFromQuery} indirectly where the latter case ensures to stay on nearly the same page as before
     * the query update.
     *
     * @return {number}
     */
    public get page(): number {
        return this._page + 1;
    }

    /**
     * Storage for the size property.
     *
     * @private
     * @type {number}
     */
    private _size: number = 0;

    /**
     * The page size which belongs to the last query the paginator was updated with.
     *
     * @return {number}
     */
    public get size(): number {
        return this._size;
    }

    /**
     * The starting element index represented by the current page and size.
     *
     * @return {number}
     */
    public get from(): number {
        return this._page * this._size;
    }

    /**
     * The index of the end of the range represented by the current page and size.
     *
     * @return {number}
     */
    public get to(): number {
        return this._page * this._size + this._size;
    }

    /**
     * Moves one page back. In case the current page is the first, then it circulates to the last page.
     */
    public previous(): void {
        this._page = (this._page - 1) % this.pageCount;
    }

    /**
     * Moves one page forward. In case the current page is the last, then it circulates to the first page.
     */
    public next(): void {
        this._page = (this._page + 1) % this.pageCount;
    }

    /**
     * The total number of pages available based on the current element count.
     *
     * @return {number}
     */
    public get pageCount(): number {
        return Math.ceil(this.elementCount / this._size);
    }

    /**
     * Provides the largest page size. Useful to prefetch data to always have enough data when the carousel updates its
     * layout due to media query changes.
     *
     * @return {number}
     */
    public get largestPageSize(): number {
        const sizes: Array<number> = [];

        BootstrapBreakpoints.getAllNames().forEach(name => sizes.push(Object(this.pagination)[name]));

        return Math.max.apply(Math, sizes);
    }

    /**
     * Checks whether the given page and size reflect the current state of the paginator.
     *
     * @param {number} page
     * @param {number} size
     *
     * @return {boolean}
     */
    public matches(page: number, size: number): boolean {
        return this.page === page && this.size === size;
    }

    /**
     * Checks whether the given page and size intersects with the current state of the paginator.
     *
     * @param {number} page
     * @param {number} size
     *
     * @return {boolean}
     */
    public intersects(page: number, size: number): boolean {
        const from: number = page * size;
        const to: number = from + size;

        // either the paginator's range starts after the given one or vice versa
        return !(this.from > to || from > this.to);
    }
}