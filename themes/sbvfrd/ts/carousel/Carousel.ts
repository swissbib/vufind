import ConfigurationItem from "./ConfigurationItem";
import MediaQueryObserver from "../common/MediaQueryObserver";
import BootstrapBreakpoints from "../common/BootstrapBreakpoints";
import Paginator from "./Paginator";
import DataLoader from "./DataLoader";

/**
 * Maintains a single carousel instance.
 */
export default class Carousel {

    /**
     * @private
     * @type {boolean}
     */
    private initialized: boolean = false;

    /**
     * @private
     * @type {Paginator}
     */
    private paginator: Paginator;

    /**
     * @private
     * @type {DataLoader}
     */
    private loader: DataLoader;

    /**
     * @private
     * @type {JQuery}
     */
    private carouselElement: JQuery<HTMLElement>;

    /**
     * @private
     * @type {JQuery}
     */
    private previousSlideControl: JQuery<HTMLElement>;

    /**
     * @private
     * @type {JQuery}
     */
    private nextSlideControl: JQuery<HTMLElement>;

    /**
     * Constructor.
     *
     * @param {ConfigurationItem} configuration
     * The carousel configuration item that contains the information for this instance.
     */
    constructor(readonly configuration:ConfigurationItem, readonly mediaQueryObserver: MediaQueryObserver) {

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

        this.previousSlideControl = this.carouselElement.find('left carousel-control');
        this.previousSlideControl.click(this.previous);

        this.nextSlideControl = this.carouselElement.find('right carousel-control');
        this.nextSlideControl.click(this.next);

        this.paginator = new Paginator(this.configuration.pagination);

        // force initialization of paginator and run first search request
        this.mediaQueryObserverCallback(this.mediaQueryObserver.match(BootstrapBreakpoints.allMobileFirst()));
    }

    /**
     * Moves to the previous slide.
     *
     * @private
     */
    private previous = (): void => {
        // TODO: implement method
        // steps:
        // update paginator (previous)
        this.paginator.previous();
        // load results from paginator infos
        this.loader.load(this.paginator, this.dataLoaded);

        // apply search result (see dataLoaded)
        //     switch to loaded slide (implicit action which may not result in page changes
        //     of this method in case another page change triggered asynchronous processes

    };

    /**
     * Moves to the next slide.
     *
     * @private
     */
    private next = ():void => {
        // TODO: implement method
        // steps:
        // update paginator (next)
        this.paginator.next();
        // load results from paginator infos
        this.loader.load(this.paginator, this.dataLoaded);
        // apply search result (see dataLoaded)
        //     switch to loaded slide (implicit action which may not result in page changes
        //     of this method in case another page change triggered asynchronous processes
    };

    /**
     * Callback that is invoked automatically each time the media query observer recognizes a relevant change of the
     * screen size.
     *
     * @private
     */
    private mediaQueryObserverCallback = (query: string): void => {
        // TODO: implement method
        // steps:
        // update paginator (from query)
        // force relayout of slides
        // (re)activate current page from paginator info
    };

    /**
     * Callback that is invoked whenever the data loader received new results.
     *
     * @private
     */
    private dataLoaded = (page: number, size: number): void => {
        // TODO: implement method
        // steps:
        // check if page and size meets the current paginator state
        //     if so: apply data directly
        //     otherwise: resolve data location from paginator and update then if necessary
    }
}