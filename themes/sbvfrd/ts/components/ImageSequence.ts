/**
 * Utility component used to register img elements with a sequence of image sources used as fallbacks.
 */
export default class ImageSequence {

    private static registry: Array<ImageSequenceEntry> = [];

    public static register(image: JQuery<HTMLElement>, paths: Array<string>): ImageSequenceEntry {
        const entry: ImageSequenceEntry = new ImageSequenceEntry(image, paths || []);
        this.registry.push(entry);
        return entry;
    }
}

/**
 *
 */
export class ImageSequenceEntry {

    constructor(private image: JQuery<HTMLElement>, private paths: Array<string>) {
        image.on('error', this.errorHandler);
    }

    public hasNext(): boolean {
        return this.paths.length > 0;
    }

    public next(): string {
        return this.hasNext() ? this.paths[0] : null;
    }

    public process(): void {
        if (this.hasNext()) {
            this.image.attr('src', this.paths.shift());
        }
    }

    private errorHandler = () => {
        this.process();
    }
}