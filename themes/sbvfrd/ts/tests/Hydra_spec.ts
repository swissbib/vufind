import Axios from "axios";
import * as fs from "fs";
import BibliographicDetails from "../BibliographicDetails";
import Hydra from "../Hydra";

const cut: Hydra = new Hydra(
    "http://data.swissbib.ch/",
);

function getPromiseFromFile(config: object): Promise<object> {
    return new Promise((resolve, reject) => {
        const lookfor = config.params.lookfor ? config.params.lookfor : config.params["overrideIds[]"];
        const id: string = lookfor
            .replace(/,/g, "_")
            .replace(/\[|\]|\s+/g, "");
        fs.readFile(`themes/sbvfrd/ts/tests/fixtures/${ config.params.type }-${ id }.json`, "utf8",
            (err: any, data: any) => {
                if (err) {
                    reject(err);
                }
                resolve(
                    {data: JSON.parse(data)});
            });
    });
}

beforeEach(() => {
    Axios.request = jest.fn().mockImplementation((config: object) => {
        return getPromiseFromFile(config);
    });
});

// jasmine.DEFAULT_TIMEOUT_INTERVAL = 1000000000;

it("Should load json", () => {
    const actual: Promise<BibliographicDetails> = cut.getBibliographicDetails("023426233");
    expect.assertions(2);
    // Return to evaluate promise
    return actual.then((details: BibliographicDetails) => {
        expect(details.persons.split(",")).toHaveLength(4);
        expect(details.persons).toContain("145a6d92-7afa-3589-aba8-28e9aec9b03d");
    });
});

it("should call api with id", () => {
    const spy = jest.spyOn(Axios, "request");

    const actual = cut.getBibliographicDetails("023426233");
    expect(spy).toHaveBeenCalled();
    expect(spy).lastCalledWith(expect.objectContaining({
        params: {
            lookfor: "023426233",
            method: "getBibliographicResource",
            searcher: "ElasticSearch",
            type: "bibliographicResource",
        },
    }));
    spy.mockReset();
    spy.mockRestore();
});

it("should return all contributor details", () => {
    const contributorIds: string = `145a6d92-7afa-3589-aba8-28e9aec9b03d,
        ba2caead-4d66-344e-80d5-ddc6c1b87523,
        5f679432-5f41-3bd8-a19f-8a20c4431aea,
        d792881e-1e3e-36df-8489-a6c3ba957e24`;

    const actual: Promise<object[]> = cut.getPersonDetails(contributorIds);

    expect.assertions(4);

    return actual.then((response: any) => {
        for (const contributor of response.data) {
            expect(contributor).toHaveProperty("firstName");
        }
    });
});
