

interface JQuery<TElement extends Node = HTMLElement> extends Iterable<TElement> {
    /**
     * @param {string | any} options
     * @returns {this}
     */
    carousel: (options: string | number | CarouselOptions) => void;
}

interface CarouselOptions {
    readonly interval: string | boolean;
    readonly pause: string | boolean;
    readonly wrap: boolean;
}