import {Detail} from "./Detail";

export class Person implements Detail {
    public hasSufficientData: boolean;
    public type: string;
    public id: string;
    public firstName: string;
    public lastName: string;
    public name: string;
}
