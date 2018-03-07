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
    constructor(private contentElement:JQuery<HTMLElement>, private trigger:JQuery<HTMLElement>) { }

    /**
     * 
     */
    public initialize(): void {
        if (!this.initialized) {
            this.trigger.on("click", this.triggerClickHandler);
            this.initialized = true;
        }
    }

    /**
     * 
     */
    triggerClickHandler = (event:JQuery.Event) => {
        event.preventDefault();
        event.stopPropagation();
        this.contentElement.addClass("visible");
        //this.trigger.addClass("overflow-hidden");
        this.trigger.addClass("hidden");
    };
}