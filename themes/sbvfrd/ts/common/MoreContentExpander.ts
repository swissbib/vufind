import MediaQueryObserver from "./MediaQueryObserver";

/**
 * 
 */
export default class MoreContentExpander {
   
    /**
     * 
     */
    private initialized: boolean;


    /**
     * 
     */
    constructor(readonly mediaQueryObserver: MediaQueryObserver, private text:JQuery<HTMLElement>, private overflow:JQuery<HTMLElement>, private trigger:JQuery<HTMLElement>) { }

    /**
     * 
     */
    public initialize(): void {
        if (!this.initialized) {
            if (this.overflow.length > 0) {
                this.trigger.on("click", this.triggerClickHandler);
                this.mediaQueryObserver.register("only screen and (min-width: 481px)", this.observerCallback);
            }
            this.initialized = true;
        }
    }

    /**
     * 
     */
    private triggerClickHandler = (event:JQuery.Event): void => {
        event.preventDefault();
        event.stopPropagation();

        this.text.removeClass("overflow-hidden");
        this.overflow.removeClass("hidden-xs").removeClass("hidden");
        this.trigger.addClass("hidden").removeClass("visible-xs-inline");
    };

    /**
     * 
     */
    private observerCallback = (query: string): void => {
        if (!this.trigger.hasClass("hidden") && this.overflow.is(":visible")) {
            this.trigger.addClass("visible-xs-inline");
        }
    }
}