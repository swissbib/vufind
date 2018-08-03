import Detail from "./Detail";

export default class Organisation implements Detail {
    public hasSufficientData: boolean;
    public type: string;
    public id: string;
    public name: string;
}
