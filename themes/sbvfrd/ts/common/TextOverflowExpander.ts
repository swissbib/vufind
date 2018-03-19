import MediaQueryObserver from "./MediaQueryObserver";
import Breakpoints from "./Breakpoints";

/**
 * Utility class that maintains a text that exceeds specific limits in length.
 */
export default class TextOverflowExpander {
   
    /**
     * Indicates whether the component has been initialized already.
     */
    private initialized: boolean;


    /**
     * Constructor.
     * 
     * @param {MediaQueryObserver} mediaQueryObserver
     * Used to observe specific breakpoints to update the appearance of the overflow elements.
     * 
     * @param {JQuery<HTMLElement>} text
     * The text to show on all screen sizes.
     * 
     * @param {JQuery<HTMLElement>} overflow
     * A list of text segements which exceed specific limits in length. Non of the overflow elements is shown on
     * displays that match the 'xs' constraints of Bootstrap's responsive breakpoints. The first overflow is shown
     * an any other display size. All overflows become visible when the trigger component has been clicked.
     * 
     * @param {JQuery<HTMLElement>} trigger
     * The element(s) that will trigger (on click) the expansion of all overflow elements. Once the expansion was
     * triggered all overflow elements stay visible regardless of the display size and the trigger will disappear.
     * Note that the trigger becomes hidden when the screen size is larger than the maximum width of the Bootstrap
     * 'xs' display constraints an re-appears when the screen matches these constraints again.
     */
    constructor(
        readonly mediaQueryObserver: MediaQueryObserver,
        private text:JQuery<HTMLElement>,
        private overflow:JQuery<HTMLElement>,
        private trigger:JQuery<HTMLElement>
    ) { }

    /**
     * Initializes the component by registering media query observer callbacks and listens to clicks on the trigger.
     */
    public initialize(): void {
        if (!this.initialized) {
            if (this.overflow.length > 0) {
                this.trigger.on("click", this.triggerClickHandler);
                this.mediaQueryObserver.register(Breakpoints.BOOTSTRAP.xs, this.observerCallback);
                this.mediaQueryObserver.register(Breakpoints.BOOTSTRAP.sm, this.observerCallback);
                this.mediaQueryObserver.register(Breakpoints.BOOTSTRAP.md, this.observerCallback);
                this.mediaQueryObserver.register(Breakpoints.BOOTSTRAP.lg, this.observerCallback);
            }
            this.initialized = true;
        }
    }

    /**
     * @private
     */
    private triggerClickHandler = (event:JQuery.Event): void => {
        event.preventDefault();
        event.stopPropagation();

        this.text.removeClass("indicator");
        this.overflow.removeClass("indicator").removeClass("hidden");
        this.trigger.remove();

    };

    /**
     * @private
     */
    private observerCallback = (query: string): void => {
        if (this.trigger.parent().length !== 0) { 
            // trigger was not removed from DOM, which implies it was not clicked
            if (query === Breakpoints.BOOTSTRAP.xs) {
                // handle xs case
                this.text.addClass("indicator");
                this.overflow.addClass("hidden").first().removeClass("indicator");
                this.trigger.removeClass("hidden");
            } else if (Breakpoints.BOOTSTRAP.isOneOf(query, "sm", "md", "lg")) {
                // handle any other case
                this.text.removeClass("indicator");
                this.overflow.first().removeClass("hidden");

                if (this.overflow.length < 2) {
                    this.trigger.addClass("hidden");
                } else {
                    this.overflow.addClass("indicator");
                }
            }
        }
    }
}