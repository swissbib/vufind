/**
 * A result object containing data fetched from the search backend for the carousel.
 */
import DataEntry from "./DataEntry";

export default class SearchResult {

    constructor(
        readonly entries:Array<DataEntry>,
        readonly page: number,
        readonly size: number
    ) { }

    public get offset(): number {
        return this.page * this.size;
    }

    public get empty(): boolean {
        return this.entries.length === 0;
    }
}