import Configuration from "./Configuration";
import MediaQueryObserver from "../common/MediaQueryObserver";
import Carousel from "./Carousel";
import ConfigurationItem from "./ConfigurationItem";

/**
 * Managing component for Bootstrap Carousel components
 */
export default class CarouselManager {

    /**
     * All managed carousels.
     *
     * @private
     * @type {{[]}}
     */
    private carousels: {[key: string]: Carousel} = { };

    /**
     * @private
     * @type {boolean}
     */
    private initialized: boolean;

    /**
     * Constructor.
     *
     * @param {Configuration} configuration
     * The carousel configuration used by this component to start maintenance of all carousels available.
     *
     * @param {MediaQueryObserver} mediaQueryObserver
     * The query observer used by the carousel to get notified on query changes so it can modify the it's slides.
     */
    constructor(readonly configuration: Configuration, readonly mediaQueryObserver: MediaQueryObserver) { }

    /**
     * Initializes the component and starts
     */
    public initialize(): void {
        if (!this.initialized) {
            this.setupFromConfiguration();
            this.initialized = true;
        }
    }

    /**
     * Activates the manager by switching on the media query observer.
     */
    public activate(): void {
        this.mediaQueryObserver.on();
    }

    /**
     * Initializes from the configuration passed in to the constructor.
     */
    private setupFromConfiguration():void {
        this.configuration.identifiers().forEach(id => this.setup(id));
    }

    /**
     * Initializes a single carousel component
     *
     * @param {string} identifier
     * The carousel identifier.
     */
    private setup = (identifier: string): void => {
        const configuration: ConfigurationItem = this.configuration.get(identifier);
        this.carousels[identifier] = new Carousel(configuration, this.mediaQueryObserver);
        this.carousels[identifier].initialize();
    };
}