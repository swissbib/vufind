import {Hydra} from "../Hydra";
import Promise from "ts-promise";
import Axios from "axios";

const fs = require("fs");

const cut: Hydra = new Hydra(
    "http://data.swissbib.ch/"
);

function getPromiseFromFile(url : string) : Promise<Object> {
    return new Promise((resolve, reject) => {
        let parts = url.split("/");
        fs.readFile(`themes/sbvfrd/ts/fixtures/${ parts[parts.length - 2] }-${ parts[parts.length - 1] }.json`, "utf8",
            (err : any, data : any) => {
                if (err) {
                    reject(err);
                }
                resolve(
                    {"data": JSON.parse(data)});
            });
    });
}

beforeEach(() => {
    Axios.get = jest.genMockFunction().mockImplementation(function (url) {
        return getPromiseFromFile(url)
    });
});

// jasmine.DEFAULT_TIMEOUT_INTERVAL = 1000000000;

it("Should load json", () => {
    const actual: Promise<any> = cut.getContributorUrls("023426233");
    expect.assertions(2);
    // Return to evaluate promise
    return actual.then((contributorUrls : string[]) => {
        expect(contributorUrls).toHaveLength(10);
        expect(contributorUrls).toContain("http://data.swissbib.ch/person/85d9afdd-b7bf-34ba-a5c7-a4c1df5c0b04");
    });
});

it("should call api with id", function () {
    const spy = jest.spyOn(Axios, "get");

    let actual = cut.getContributorUrls("023426233");
    expect(spy).toHaveBeenCalled();
    expect(spy).lastCalledWith(expect.stringMatching("^.*/023426233$"), expect.anything());
    spy.mockReset();
    spy.mockRestore();
});

it("should return contributor detail", () => {
    const actual = cut.getContributorDetail("http://data.swissbib.ch/person/145a6d92-7afa-3589-aba8-28e9aec9b03d");
    expect.assertions(1);
    return actual.then(response => {
        expect(response.data).toHaveProperty("abstract");
    });
});


it("should return all contributor details", () => {
    const contributorUrls : string[] = [
        "http://data.swissbib.ch/person/145a6d92-7afa-3589-aba8-28e9aec9b03d",
        "http://data.swissbib.ch/person/ba2caead-4d66-344e-80d5-ddc6c1b87523",
        "http://data.swissbib.ch/person/5f679432-5f41-3bd8-a19f-8a20c4431aea"/*,
        "http://data.swissbib.ch/person/d792881e-1e3e-36df-8489-a6c3ba957e24",
        "http://data.swissbib.ch/person/66f120d2-5d83-31dc-92dd-3ef48bd3a7c3",
        "http://data.swissbib.ch/person/85d9afdd-b7bf-34ba-a5c7-a4c1df5c0b04",
        "http://data.swissbib.ch/person/76100df3-36d4-301f-b0ad-15912eba659d",
        "http://data.swissbib.ch/person/aed5dd75-7b3b-369c-93bb-97a6f41d344c",
        "http://data.swissbib.ch/person/93a0ddb8-7b96-3b6d-8424-68f5ec63680d",
        "http://data.swissbib.ch/person/f2c6034e-2636-32e2-9db8-5737a263061c"*/
    ];
    const actual : Promise<object>[] = cut.getContributorDetails(contributorUrls);

    expect.assertions(actual.length);

    return Promise.all((actual)).then((contributors : Promise<object>) => {
        for (let contributor of contributors) {
            expect(contributor).toHaveProperty("firstName");
        }
    });
});

it("should create Html", () => {
    expect.assertions(1);

    const actual = cut.getContributorHtml(getPromiseFromFile( "http://data.swissbib.ch/person/5f679432-5f41-3bd8-a19f-8a20c4431aea"));
    return expect(actual).resolves.toContain("<li>Bamber, David");
});

it("Empty should be not sufficient info", () => {
    let given = {};
    const actual = cut.personHasSufficientData(given);
    expect(actual).toBeFalsy();
});

it("Only 7 elements should be not sufficient info", () => {
    let given = {"1": "", "2": "", "3": "", "4": "", "5": "", "6": "", "7": ""};
    const actual = cut.personHasSufficientData(given);
    expect(actual).toBeFalsy();
});

it("8 elements should be sufficient info", () => {
    let given = {"1": "", "2": "", "3": "", "4": "", "5": "", "6": "", "7": "", "8": ""};
    const actual = cut.personHasSufficientData(given);
    expect(actual).toBeTruthy();
});