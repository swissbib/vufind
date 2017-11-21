import Axios from "axios";
import * as fs from "fs";
import * as $ from "jquery";
import {Hydra} from "../Hydra";

const cut: Hydra = new Hydra(
    "http://data.swissbib.ch/",
);

function getPromiseFromFile(url: string): Promise<object> {
    return new Promise((resolve, reject) => {
        const parts = url.split("/");
        fs.readFile(`themes/sbvfrd/ts/fixtures/${ parts[parts.length - 2] }-${ parts[parts.length - 1] }.json`, "utf8",
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
    Axios.get = jest.genMockFunction().mockImplementation((url: string) => {
        return getPromiseFromFile(url);
    });
});

// jasmine.DEFAULT_TIMEOUT_INTERVAL = 1000000000;

it("Should load json", () => {
    const actual: Promise<any> = cut.getContributorUrls("023426233");
    expect.assertions(2);
    // Return to evaluate promise
    return actual.then((contributorUrls: string[]) => {
        expect(contributorUrls).toHaveLength(4);
        expect(contributorUrls).toContain("http://data.swissbib.ch/person/145a6d92-7afa-3589-aba8-28e9aec9b03d");
    });
});

it("should call api with id", () => {
    const spy = jest.spyOn(Axios, "get");

    const actual = cut.getContributorUrls("023426233");
    expect(spy).toHaveBeenCalled();
    expect(spy).lastCalledWith(expect.stringMatching("^.*/023426233$"), expect.anything());
    spy.mockReset();
    spy.mockRestore();
});

it("should return contributor detail", () => {
    const actual = cut.getContributorDetail("http://data.swissbib.ch/person/145a6d92-7afa-3589-aba8-28e9aec9b03d");
    expect.assertions(1);
    return actual.then((response) => {
        expect(response.data).toHaveProperty("abstract");
    });
});

it("should return all contributor details", () => {
    const contributorUrls: string[] = [
        "http://data.swissbib.ch/person/145a6d92-7afa-3589-aba8-28e9aec9b03d",
        "http://data.swissbib.ch/person/ba2caead-4d66-344e-80d5-ddc6c1b87523",
        "http://data.swissbib.ch/person/5f679432-5f41-3bd8-a19f-8a20c4431aea",
    ];
    const actual: Array<Promise<object>> = cut.getContributorDetails(contributorUrls);

    expect.assertions(actual.length);

    return Promise.all((actual)).then((contributors: Promise<object>) => {
        for (const contributor of contributors) {
            expect(contributor).toHaveProperty("firstName");
        }
    });
});

it("should create Html", () => {
    expect.assertions(1);
    window.VuFind = {
        path: "",
    };
    const contributorPromise = getPromiseFromFile("http://data.swissbib.ch/person/5f679432-5f41-3bd8-a19f-8a20c4431aea")
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

it("Only 7 elements should be not sufficient info", () => {
    const given = {1: "", 2: "", 3: "", 4: "", 5: "", 6: "", 7: ""};
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
        expect(actual.find("li").get(1).innerHTML).toEqual("Jennifer");
    });
});
