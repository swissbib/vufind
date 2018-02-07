/**
 * Defines constants for Bootstrap's media query breakpoints.
 */
export default class BootstrapBreakpoints {

    static readonly XS: string = "only screen and (min-width : 480px) and (max-width: 767px)";

    static readonly SM: string = "only screen and (min-width : 768px) and (max-width: 991px)";

    static readonly MD: string = "only screen and (min-width : 992px) and (max-width: 1199px)";

    static readonly LG: string = "only screen and (min-width : 1200px)";


    public static allMobileFirst(): Array<string> {
        return [ this.XS, this.SM, this.MD, this.LG ]
    }


    private static names: {[key: string]: string} = {};

    public static getName(breakpoint: string): string {
        return this.names.hasOwnProperty(breakpoint) ? this.names[breakpoint] : null;
    }

    public static getAllNames(): Array<string> {
        const names: Array<string> = [];
        Object(this.names).keys().map((key: string) => names.push(this.names[key]));
        return names;
    }

    public static initialize(): void {
        this.names[this.LG] = "lg";
        this.names[this.MD] = "md";
        this.names[this.SM] = "sm";
        this.names[this.XS] = "xs";
    }
}

BootstrapBreakpoints.initialize();