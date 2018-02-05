import Carousel from "./Carousel";
import SearchResult from "./SearchResult";
import Templates from "../common/Templates";
import Paginator from "./Paginator";
import DataCache from "./DataCache";
import DataEntry from "./DataEntry";

/**
 * Loads data for a carousel.
 */
export default class DataLoader {

    /**
     * @private
     * @type {DataCache}
     */
    private cache: DataCache = new DataCache();

    /**
     * Constructor.
     *
     * @param {Carousel} carousel
     * The carousel to load data into.
     */
    constructor(readonly carousel: Carousel) { }

    public load(paginator: Paginator, callback: (page: number, size: number) => void): void {
        const page: number = paginator.currentPage;
        const size: number = paginator.currentPageSize;

        if (this.cache.containsRange(page * size, page * size + size)) {
            callback(page, size);
        } else {
            this.requestData(page, size, callback);
        }
    }

    private requestData(page: number, size: number, callback: (page: number, size: number) => void) {
        const loader: DataLoader = this;
        $.ajax({
            dataType: "json",
            success: (result: Array<DataEntry>) => {
                loader.processResult(result, page, size);
                callback(page, size);
            },
            url: this.getSearchUrl(page, size)
        });
    }

    public getData(page: number, size: number): Array<SearchResult> {
        return null;
    }

    private getSearchUrl(page: number, size: number): string {
        const template: string = this.carousel.configuration.template;
        const replacements:{[key: string]: any} = {
            "page": page,
            "size": size
        };

        return (new Templates()).resolve(template, replacements);
    }

    private processResult = (result: Array<DataEntry>, page: number, size: number): void => {

    }
}