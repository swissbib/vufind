/**
 * Caching component for data loaded for a carousel.
 */
export default class CarouselDataCache {

    /**
     * @private
     * @type {Array<any>}
     */
    private cache:Array<any>;

    /**
     * Constructor.
     */
    constructor() { }


    public containsRange(from: number, to: number): boolean {
        return this.cache.length > to && this.checkRange(from, to);
    }

    private checkRange(from: number, to: number): boolean {
        let result: boolean = true;

        for (let index = from; index < to; ++index) {
            if (!this.cache[index]) {
                result = false;
                break;
            }
        }

        return result;
    }
}