import Carousel from "./Carousel";
import SearchResult from "./SearchResult";
import DataEntry from "./DataEntry";
import Templates from "./Templates";
import Paginator from "./Paginator";

/**
 * A component to apply layout changes when paginator changes due to media query updates.
 */
export default class Renderer {

    private templates: Templates;

    /**
     * Constructor.
     *
     * @param {Carousel} carousel
     * The carousel object the layout operates on.
     */
    constructor(readonly carousel: Carousel) {
        this.templates = new Templates(carousel.configuration);
    }

    /**
     * Renders all slides based on the carousel's configuration and paginator information. This method will always
     * regenerate all slides and applies all available data.
     */
    public render(): void {
        this.renderSlides();

        const result: SearchResult = this.carousel.getData();

        if (!result.empty) {
            this.applyResult(result);
        }
    }

    /**
     * Applies data entries according to the given page and size. When page and size are not matching the carousel's
     * paginator state page, then following will happen:
     * 1) When either page or size is not a number or page is less than zero or size less than one, then all data
     *    entries available will be applied.
     * 2) When page and size are greater than zero or one respectively and not completely exceeding the total amount of
     *    entries, then page and size will be remapped to match the correct range of entries to apply to the current
     *    pagination state.
     * 3) When page and size are positive integers and completely exceeding the total of entries, then nothing happens.
     *
     * @param {number} page
     * The page to apply data for.
     *
     * @param {number} size
     * The size of the page to apply data for.
     */
    public apply(page?: number, size?: number): void {
        let result: SearchResult;

        if (isNaN(page) || isNaN(size) || page < 0 || size < 1) {
            result = this.carousel.getData(); // retrieve the whole data entry collection
        } else if ((page * size) < this.carousel.configuration.total) {
            // pages start at one while indices are zero-based
            result = this.carousel.getData(page, size);
        } else {
            result = new SearchResult();
        }

        if (!result.empty) {
            this.applyResult(result);
            // TODO: Move this to app.ts and send notifications when to perform lightbox updates
            VuFind.lightbox.init();
        }
    }

    /**
     * Renders all slides based on the total amount of entries given by the carousel configuration and its paginator
     * state.
     *
     * @private
     */
    private renderSlides(): void {
        const size: number = this.carousel.paginator.size;
        const numSlides: number = Math.floor(this.carousel.configuration.total / size);
        const remaining: number = this.carousel.configuration.total % size;
        const slideIndex: number = this.getActiveSlideIndex();

        this.carousel.slideContainerElement.empty();

        for (let slide: number = 0; slide < numSlides; ++slide) {
            this.renderSlide(size);
        }

        if (remaining > 0) {
            // we have remaining columns when the total amount of entries cannot be spread evenly
            this.renderSlide(size, remaining);
        }

        this.apply();
        this.restoreSlideIndex(slideIndex);
    }

    /**
     * Maps the slide that is currently active from the previous page size to the new one, so the active slide can be
     * restored after re-rendering.
     *
     * @private
     * @return {number}
     */
    private getActiveSlideIndex(): number {
        const container: JQuery<HTMLElement> = this.carousel.slideContainerElement;
        const current: Paginator = this.carousel.paginator;
        const previous: Paginator = current.lastState;

        const slide: number = container.find(".item").index(container.find(".item.active"));

        // map currently active slide to new paging
        return slide < 1 ? 0 : Math.floor((slide * previous.size) / current.size);
    }

    /**
     * Applies the given slide index to restore active slide after re-rendering.
     *
     * @private
     * @param {number} index
     * The index of the slide to restore.
     */
    private restoreSlideIndex(index: number): void {
        $(this.carousel.slideContainerElement.find(".item").get(index)).addClass("active");
    }


    /**
     * Renders a single slide based on the given size.
     *
     * @private
     *
     * @param {number} size
     * Used to determine the column sizes and the number of columns to generate.
     *
     * @param {number} remaining
     * When greater than zero, then only the number of columns will be generated which is expressed by this parameter.
     * Otherwise size is used.
     */
    private renderSlide(size: number, remaining: number = 0): void {
        const template: string = this.templates.slide(size, remaining);
        const element: JQuery<HTMLElement> = $(template);

        this.carousel.slideContainerElement.append(element);
    }

    /**
     * Applies the data entries of the given result.
     *
     * @private
     * @param {SearchResult} result
     */
    private applyResult(result: SearchResult): void {
        const from: number = result.containsAll ? 0 : (result.page) * result.size;
        const to: number = result.containsAll ? result.entries.length : (from + result.size);
        const container: JQuery<HTMLElement> = this.carousel.slideContainerElement;
        const elements:JQuery<HTMLElement> = container.find('div[class^="col-xs-"]').slice(from, to);

        result.entries.forEach((entry: DataEntry, index: number): void => {
            const element: JQuery<HTMLElement> = $(elements.get(index));
            const template: string = this.templates.entry(entry);
            //console.log(element, template);
            element.empty().append($(template));
        });
    }
}