import Carousel from "./Carousel";
import SearchResult from "./SearchResult";
import DataEntry from "./DataEntry";

/**
 * A component to apply layout changes when paginator changes due to media query updates.
 */
export default class Renderer {

    /**
     * Constructor.
     *
     * @param {Carousel} carousel
     * The carousel object the layout operates on.
     */
    constructor(readonly carousel: Carousel) { }

    /**
     * Renders all available data or the current page based on the paginator's state.
     *
     * @param {boolean} useCurrentPage
     * Indicates whether to render only the data for the current page or all pages.
     */
    public render(useCurrentPage?: boolean): void {
        const page: number = this.carousel.paginator.page;
        const size: number = this.carousel.paginator.size;
        const result: SearchResult = useCurrentPage ? this.carousel.getData(page, size) : this.carousel.getData();

        this.renderResult(result, page, size);
    }


    /**
     * Renders the given result into the slide container element of the carousel
     *
     * @param {SearchResult} result
     * @param {number} page
     * @param {number} size
     */
    private renderResult(result: SearchResult, page: number, size: number): void {
        if (result.containsAll) {
            this.renderAll(result, size);
        } else {
            this.renderPage(result);
        }
    }

    private renderAll(result: SearchResult, size:number): void {
        this.carousel.slideContainerElement.empty();
        const pageCount: number = Math.ceil(result.entries.length / size);

        for (let page: number = 0; page < pageCount; ++page) {
            this.renderPage(result.getData(page, size));
        }
    }

    private renderPage(result: SearchResult): void {
        const selector: string = `> .item:nth-child(${result.page + 1})`;
        const container: JQuery<HTMLElement> = this.carousel.slideContainerElement;

        let slide: JQuery<HTMLElement> = container.find(selector);
        let row: JQuery<HTMLElement>;

        if (slide.length === 0) {
            // insert new slide
            slide = container.append(`<div class="item"></div>`);
        }

        slide.empty();
        row = slide.append(`<div class="row"></div>`);

        result.entries.forEach((entry: DataEntry) => {
            row.append(`<div class="xs-col-${result.size / 12}">${entry.name}</div>`);
        });
    }
}