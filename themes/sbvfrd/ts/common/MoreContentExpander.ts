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
    constructor(private text:JQuery<HTMLElement>, private more:JQuery<HTMLElement>, private trigger:JQuery<HTMLElement>) { }

    /**
     * 
     */
    public initialize(): void {
        if (!this.initialized) {
            if (this.more.length > 0) {
                this.trigger.on("click", this.triggerClickHandler);
                this.text.addClass('more-indicator');
            }
            this.initialized = true;
        }
    }

    /**
     * 
     */
    triggerClickHandler = (event:JQuery.Event) => {
        event.preventDefault();
        event.stopPropagation();
        this.text.toggleClass("more-indicator");
        this.more.toggleClass("visible");
        //this.trigger.addClass("overflow-hidden");
        this.trigger.toggleClass("hidden");
    };
}