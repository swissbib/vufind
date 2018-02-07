/// <reference path="../types/jquery.bootstrap.carousel.d.ts"/>

import ConfigurationItem from "./ConfigurationItem";
import MediaQueryObserver from "../common/MediaQueryObserver";
import BootstrapBreakpoints from "../common/BootstrapBreakpoints";
import Paginator from "./Paginator";
import DataLoader from "./DataLoader";
import SearchResult, {SearchResultProvider} from "./SearchResult";
import Renderer from "./Renderer";

/**
 * Maintains a single carousel instance.
 */
export default class Carousel implements SearchResultProvider {

    /**
     * @private
     * @type {boolean}
     */
    private initialized: boolean = false;

    /**
     * @private
     * @type {DataLoader}
     */
    private loader: DataLoader;

    /**
     * @private
     * @type {JQuery<HTMLElement>}
     */
    private carouselElement: JQuery<HTMLElement>;

    /**
     * @private
     * @type {JQuery<HTMLElement>}
     */
    private previousSlideControl: JQuery<HTMLElement>;

    /**
     * @private
     * @type {JQuery<HTMLElement>}
     */
    private nextSlideControl: JQuery<HTMLElement>;

    /**
     * @private
     * @type {JQuery<HTMLElement>}
     */
    private renderer: Renderer;

    /**
     * Constructor.
     *
     * @param {ConfigurationItem} configuration
     * The carousel configuration item that contains the information for this instance.
     *
     * @param {MediaQueryObserver} mediaQueryObserver
     */
    constructor(readonly configuration:ConfigurationItem, readonly mediaQueryObserver: MediaQueryObserver) { }


    /**
     * Storage for the sliderContainerElement property.
     *
     * @private
     * @type {JQuery<HTMLElement>}
     */
    private _slideContainerElement: JQuery<HTMLElement>;

    /**
     * A reference on the element that contains all slides.
     *
     * @return {JQuery<HTMLElement>}
     */
    public get slideContainerElement(): JQuery<HTMLElement> {
        return this._slideContainerElement;
    }

    /**
     * Storage for the paginator property.
     *
     * @private
     * @type {Paginator}
     */
    private _paginator: Paginator;

    /**
     * The paginator used by the carousel to keep track of the page and size based on currently matching media query.
     *
     * @return {Paginator}
     */
    public get paginator(): Paginator {
        return this._paginator;
    }

    /**
     * @inheritDoc
     */
    public getData(page?: number, size?: number): SearchResult {
        return this.loader.getData(page, size);
    }

    /**
     * Initializes the carousel by connecting to the component in the DOM that has its
     * 'id'-attribute set to the identifier of the configuration.
     */
    public initialize(): void {
        if (!this.initialized) {
            this.setupDataLoader();
            this.setupWithMediaQueryObserver();
            this.setupFromConfiguration();
            this.initialized = true;
        }
    }

    /**
     * @private
     */
    private setupDataLoader(): void {
        this.loader = new DataLoader(this);
    }

    /**
     * Registers callbacks for relevant media queries.
     */
    private setupWithMediaQueryObserver(): void {
        const observer: MediaQueryObserver = this.mediaQueryObserver;
        const callback: (query: string) => void = this.mediaQueryObserverCallback;

        BootstrapBreakpoints.allMobileFirst().forEach(query => observer.register(query, callback));
    }

    /**
     * Initializes internal state.
     *
     * @private
     */
    private setupFromConfiguration(): void {
        this.carouselElement = $(`#carousel-${this.configuration.id}`);

        this.previousSlideControl = this.carouselElement.find('.left.carousel-control');
        this.previousSlideControl.click(this.previous);

        this.nextSlideControl = this.carouselElement.find('.right.carousel-control');
        this.nextSlideControl.click(this.next);

        this._slideContainerElement = this.carouselElement.find('.carousel-inner');

        this._paginator = new Paginator(this.configuration.pagination);
        this.renderer = new Renderer(this);
    }

    /**
     * Moves to the previous slide.
     *
     * @private
     */
    private previous = (event:JQuery.Event): void => {
        event.preventDefault();

        this.paginator.previous();
        this.loader.load(this.paginator, this.dataLoaded);
    };

    /**
     * Moves to the next slide.
     *
     * @private
     */
    private next = (event:JQuery.Event):void => {
        event.preventDefault();

        this.paginator.next();
        this.loader.load(this.paginator, this.dataLoaded);
    };

    /**
     * Callback that is invoked automatically each time the media query observer recognizes a relevant change of the
     * screen size.
     *
     * @private
     */
    private mediaQueryObserverCallback = (query: string): void => {
        this.paginator.updateFromQuery(query);
        this.loader.load(this.paginator, this.dataLoaded);
    };

    /**
     * Callback that is invoked whenever the data loader received new results.
     *
     * @private
     */
    private dataLoaded = (page: number, size: number): void => {
        const useCurrentPage: boolean = this.paginator.matches(page, size);
        this.renderer.render(useCurrentPage);

        if (useCurrentPage) {
            this.carouselElement.carousel(page - 1);
        }
    }
}