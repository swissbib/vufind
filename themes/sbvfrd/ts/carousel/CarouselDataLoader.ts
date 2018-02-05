/**
 * Loads data for a carousel.
 */
import Carousel from "./Carousel";
import SearchResult from "./SearchResult";
import Templates from "../common/Templates";

export default class CarouselDataLoader {

    /**
     * Constructor.
     *
     * @param {Carousel} carousel
     * The carousel to load data into.
     */
    constructor(readonly carousel: Carousel) { }

    public load(page: number, size: number): void {
        const loader: CarouselDataLoader = this;

        $.ajax({
            dataType: "json",
            success: (result: SearchResult) => {
                loader.processResult(result, page, size);
            },
            url: this.getSearchUrl(page, size)
        });
    }

    private getSearchUrl(page: number, size: number): string {
        const template: string = this.carousel.configuration.template;
        const replacements:{[key: string]: any} = {
            "page": page,
            "size": size
        };

        return (new Templates()).resolve(template, replacements);
    }

    private processResult = (result: SearchResult, page: number, size: number): void => {

    }
}