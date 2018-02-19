/**
 * Defines breakpoint names for common screen sizes.
 */
export class BreakpointNames {

    /**
     * Name for extra small devices like smartphones.
     *
     * @type {string}
     */
    static readonly XS: string = "xs";

    /**
     * Name for small devices like tablets.
     *
     * @type {string}
     */
    static readonly SM: string = "sm";

    /**
     * Name for common laptop or external displays.
     *
     * @type {string}
     */
    static readonly MD: string = "md";

    /**
     * Name for large displays.
     *
     * @type {string}
     */
    static readonly LG: string = "lg";


    /**
     * Provides all available names.
     *
     * @return {Array<string>}
     */
    static get all():Array<string> {
        return [this.XS, this.SM, this.MD, this.LG];
    }
}


/**
 * A data structure that describes a collection of named breakpoints.
 */
export class BreakpointCollection {

    /**
     * Maps media queries to breakpoint names for easier access.
     *
     * @type {{}}
     */
    private names:{[key: string]: string} = {};

    /**
     * Constructor.
     *
     * @param {string} xs
     * Media query for extra small displays.
     *
     * @param {string} sm
     * Media query for small displays.
     *
     * @param {string} md
     * Media query for medium displays.
     *
     * @param {string} lg
     * Media query for large displays.
     */
    constructor(readonly xs: string, readonly sm: string, readonly md: string, readonly lg: string) {
        BreakpointNames.all.forEach(name => this.names[Object(this)[name]] = name);
    }

    /**
     * Provides all breakpoints in a mobile-first order.
     *
     * @return {Array<string>}
     */
    public get mobileFirst(): Array<string> {
        return [ this.xs, this.sm, this.md, this.lg ];
    }

    /**
     * Provides the name that belongs to the given breakpoint.
     *
     * @param {string} breakpoint
     * The exact media query string to search for its name in this collection.
     *
     * @return {string}
     * The name that belongs to the given breakpoint or null if not found.
     */
    public getName(breakpoint: string): string {
        return this.names.hasOwnProperty(breakpoint) ? this.names[breakpoint] : null;
    }
}


/**
 * Defines constants for Bootstrap's media query breakpoints. The breakpoints are not defined
 */
export default class Breakpoints {

    static readonly CAROUSEL: BreakpointCollection = new BreakpointCollection(
        "only screen and (max-width: 767px)",
        "only screen and (min-width: 768px) and (max-width: 991px)",
        "only screen and (min-width: 992px) and (max-width: 1199px)",
        "only screen and (min-width: 1200px)"
    );

    static readonly BOOTSTRAP: BreakpointCollection = new BreakpointCollection(
        "only screen and (max-width: 480px)",
        "only screen and (min-width: 481px) and (max-width: 768px)",
        "only screen and (min-width: 769px) and (max-width: 1199px)",
        "only screen and (min-width: 1200px)"
    );
}