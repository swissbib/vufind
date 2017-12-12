import Axios from "axios";
import * as fs from "fs";
import * as $ from "jquery";
import {Hydra} from "../Hydra";

const cut: Hydra = new Hydra(
    "http://data.swissbib.ch/",
);

function getPromiseFromFile(config: object): Promise<object> {
    return new Promise((resolve, reject) => {
        const id = config.params.lookfor
            .replace(/,/g, "_")
            .replace(/\[|\]|\s+/g, "");
        fs.readFile(`themes/sbvfrd/ts/fixtures/${ config.params.type }-${ id }.json`, "utf8",
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
    Axios.request = jest.genMockFunction().mockImplementation((config: object) => {
        return getPromiseFromFile(config);
    });
});

// jasmine.DEFAULT_TIMEOUT_INTERVAL = 1000000000;

it("Should load json", () => {
    const actual: Promise<string> = cut.getContributorIds("023426233");
    expect.assertions(2);
    // Return to evaluate promise
    return actual.then((contributorIds: string) => {
        expect(contributorIds.split(",")).toHaveLength(4);
        expect(contributorIds).toContain("145a6d92-7afa-3589-aba8-28e9aec9b03d");
    });
});

it("should call api with id", () => {
    const spy = jest.spyOn(Axios, "request");

    const actual = cut.getContributorIds("023426233");
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

    const actual: Promise<object[]> = cut.getContributorDetails(contributorIds);

    expect.assertions(4);

    return actual.then((response: any) => {
        for (const contributor of response.data) {
            expect(contributor).toHaveProperty("firstName");
        }
    });
});

it("should create Html", () => {
    expect.assertions(1);
    window.VuFind = {
        path: "",
    };
    const contributorPromise = getPromiseFromFile({
            params: {
                lookfor: "5f679432-5f41-3bd8-a19f-8a20c4431aea",
                type: "person",
            },
        },
    )
        .then((response: any) => response.data);

    const templateFn = (p: any): string => {
        return `${p.lastName}, ${p.firstName}`;
    };

    const actual = cut.getContributorHtml(
        contributorPromise, templateFn);
    return expect(actual).resolves.toContain("Bamber, David");
});

it("Empty should be not sufficient info", () => {
    const given = {};
    const actual = Hydra.personHasSufficientData(given);
    expect(actual).toBeFalsy();
});

it("Only 4 elements should be not sufficient info", () => {
    const given = {1: "", 2: "", 3: "", 4: ""};
    const actual = Hydra.personHasSufficientData(given);
    expect(actual).toBeFalsy();
});

it("8 elements should be sufficient info", () => {
    const given = {1: "", 2: "", 3: "", 4: "", 5: "", 6: "", 7: "", 8: ""};
    const actual = Hydra.personHasSufficientData(given);
    expect(actual).toBeTruthy();
});

it("Html should contain list element with contributors", () => {
    const body = document.getElementsByTagName("body")[0];
    const list = document.createElement("ul");
    body.appendChild(list);
    expect.assertions(2);
    return cut.renderContributors("023426233", $(list)[0], (p: any) => {
        return `<li>${p.firstName}</li>`;
    })
        .then((html: HTMLElement) => {
            const actual: JQuery<HTMLElement> = $(html);
            expect(actual.children("li").length).toBe(4);
            expect(actual.find("li").get(0).innerHTML).toEqual("David");
        });
});
