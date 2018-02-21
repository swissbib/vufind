/**
 * A component that maintains a given element as a back-to-top button which appears after some scrolling threshold and
 * disappears again when scroll position is less than the threshold.
 */
import MediaQueryObserver from "../common/MediaQueryObserver";
import Breakpoints, {BreakpointNames} from "../common/Breakpoints";

export default class BackToTopButton {

    /**
     * The generated HTML element used as back-top-button.
     *
     * @private
     */
    private target: JQuery<HTMLElement>;

    /**
     * Constructor.
     *
     * @param {string} dom
     * The target DOM element to maintain as back-to-top button.
     *
     * @param {number} threshold
     * The amount of pixels to scroll before the button has to appear.
     */
    constructor(private dom: string, public threshold: number = 200) {
    }


    /**
     * Initializes the component by starting to listen to the window scroll event.
     */
    public initialize(): void {
        this.target = $(this.dom).appendTo('body');
        this.target.click(this.targetClickHandler);

        $(window).scroll(this.windowScrollHandler);
    }

    /**
     * Monitors the vertical scroll position to show/hide the button.
     *
     * @private
     */
    private windowScrollHandler = (): void => {
        const names:Array<string> = [BreakpointNames.XS, BreakpointNames.SM];
        if (MediaQueryObserver.matchesNames(Breakpoints.BOOTSTRAP, names)) {
            console.log("use back-to-top button");
            if ($(window).scrollTop() > this.threshold) {
                this.target.fadeIn(200);
            } else {
                this.target.fadeOut(200);
            }
        } else {
            this.target.fadeOut(200);
        }
    };

    /**
     * Click handler for the back-to-top button.
     *
     * @private
     */
    private targetClickHandler = (event:JQuery.Event): void => {
        event.preventDefault();
        $('html, body').animate({scrollTop: 0}, 300);
    };
}