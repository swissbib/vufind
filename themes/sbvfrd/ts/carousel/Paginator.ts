import Pagination from "./Pagination";
import Breakpoints from "../common/Breakpoints";

/**
 * Holds the pagination state of a carousel and provides
 */
export default class Paginator {

    /**
     * The maximum number of elements to display in the carousel. The carousel will never show more then
     * MAX_ELEMENT_COUNT elements. Means, that the minimum of MAX_ELEMENT_COUNT and the constructor's elementCount
     * parameter will be used as maximum.
     *
     * @type {number}
     */
    private static readonly MAX_ELEMENT_COUNT: number = 120;

    /**
     * Constructor.
     *
     * @param {Pagination} pagination
     * The pagination information to calculate new paging values from.
     *
     * @param {number} elementCount
     * The total number of elements the paginator has to take into account for paging calculations.
     */
    constructor(readonly pagination:Pagination, readonly elementCount: number = 120) {
        this.elementCount = Math.min(Paginator.MAX_ELEMENT_COUNT, elementCount);
    }

    /**
     * Recalculates all values of the paginator based on the given query.
     *
     * @param {String} query
     */
    public updateFromQuery(query: string): void {
        this._lastState = this.clone();

        const name = Breakpoints.CAROUSEL.getName(query);
        const newPageSize = Object(this.pagination)[name];

        this._page = Math.floor((this.page * this.size) / newPageSize);
        this._size = newPageSize;
    }

    /**
     * Storage for the lastState property.
     *
     * @private
     * @type {Paginator}
     */
    private _lastState: Paginator;

    /**
     * Last pagination state before {@link #next}, {@link #previous} or {@link #updateFromQuery} was called.
     *
     * @return {Paginator}
     */
    public get lastState(): Paginator {
        if (!this._lastState) {
            this._lastState = this.clone();
        }
        return this._lastState;
    }

    /**
     * Creates a copy of this paginator.
     *
     * @return {Paginator}
     */
    public clone(): Paginator {
        const copy: Paginator = new Paginator(this.pagination, this.elementCount);

        copy._size = this._size;
        copy._page = this._page;

        return copy;
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
        return this._page;
    }

    /**
     * Storage for the size property.
     *
     * @private
     * @type {number}
     */
    private _size: number = 1;

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
        return this.page * this.size;
    }

    /**
     * The index of the end of the range represented by the current page and size.
     *
     * @return {number}
     */
    public get to(): number {
        return this.page * this.size + this.size;
    }

    /**
     * Moves one page back. In case the current page is the first, then it circulates to the last page.
     */
    public previous(): void {
        this._lastState = this.clone();
        // add pageCount to always have positive page values
        this._page = (this.page - 1 + this.pageCount) % this.pageCount;
    }

    /**
     * Moves one page forward. In case the current page is the last, then it circulates to the first page.
     */
    public next(): void {
        this._lastState = this.clone();
        this._page = (this.page + 1) % this.pageCount;
    }

    /**
     * The total number of pages available based on the current element count.
     *
     * @return {number}
     */
    public get pageCount(): number {
        return Math.ceil(this.elementCount / this.size);
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