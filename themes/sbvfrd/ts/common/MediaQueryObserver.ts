/**
 * This component connects to the window's 'resize' event and triggers registered callbacks whenever a specific media
 * query matches.
 */
export default class MediaQueryObserver {

    /**
     * Storage for registered callbacks.
     *
     * @private
     * @type {{[string]: any}}
     */
    private registry: {[key: string]: Array<(query: string) => void>} = {};

    /**
     * Keeps all media query strings which matched last time a resize event was received to invoke callbacks only once
     * they match.
     *
     * @private
     * @type {string[]}
     */
    private latestMatches:Array<string> = [];

    /**
     * @private
     * @type {boolean}
     */
    private observing: Boolean;

    /**
     * Registers the given callback under the specified media query.
     *
     * @param {string} query
     * The media query to react on.
     *
     * @param {() => void} callback
     * The callback function to register it must accept a single string argument which is the matching query.
     */
    public register(query: string, callback: (query: string) => void): void
    {
        this.registry[query] = this.registry[query] || [];

        if (this.registry[query].indexOf(callback) === -1) {
            this.registry[query].push(callback);
        }
    }

    /**
     * Searches for the first matching media query and returns it.
     *
     * @param {Array<string>} queries
     * The media queries to check for matching.
     *
     * @return {string}
     * The first matching media query.
     */
    public match(queries:Array<string>): string {
        let result: string = null;

        for (let index: number = 0; index < queries.length; ++index) {
            if (window.matchMedia(queries[index])) {
                result = queries[index];
                break;
            }
        }

        return result;
    }

    /**
     * Starts listening on screen size changes.
     */
    public on(): void {
        if (!this.observing) {
            $(window).on('resize', this.windowResizeHandler);
            this.observing = true;
        }
    }

    /**
     * Stops listening on screen size changes.
     */
    public off(): void {
        if (this.observing) {
            $(window).off('resize', this.windowResizeHandler);
            this.observing = false;
        }
    }

    /**
     * @private
     * @param {JQuery.Event} event
     */
    private windowResizeHandler = (event: JQuery.Event) => {
        const matches: Array<string> = [];

        for (let query in this.registry) {
            if (this.matchMedia(query, matches)) {
                matches.push(query);
            }
        }

        this.latestMatches = matches;
    };

    /**
     * @private
     * @param {string} query
     * @param {Array<string>} matches
     */
    private matchMedia(query: string, matches:Array<string>): void {
        if (window.matchMedia(query).matches) {
            this.process(query);
            matches.push(query);
        }
    }

    /**
     * @private
     * @param {string} query
     */
    private process(query: string): void {
        if (this.latestMatches.indexOf(query) === -1) {
            this.registry[query].forEach(callback => callback(query));
        }
    }
}