/**
 * An interface for data entries fetched for a carousel.
 */
export default interface DataEntry {

    /**
     * The unique identifier of the entry.
     */
    readonly id: string;

    /**
     * The URL to the thumbnail of the represented data entry.
     */
    readonly thumbnail: string;

    /**
     * The name of the person, subject or media to use for further searches.
     */
    readonly name: string;

    /**
     * Name of the person, subject or media which to use for rendering.
     */
    readonly displayName: string;

    /**
     * Indicates whether there is sufficient data for this entry to show in a knowledge-card.
     */
    readonly sufficientData: boolean;
}