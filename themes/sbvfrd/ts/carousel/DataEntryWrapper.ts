/**
 * An interface for the wrapper of a data entries fetched for a carousel.
 */
import DataEntry from "./DataEntry";

export default interface DataEntryWrapper {

    /**
     * The only wrapper element there is.
     */
    readonly data: Array<DataEntry>;

}