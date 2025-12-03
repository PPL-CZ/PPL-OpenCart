import { useQuery } from "@tanstack/react-query";
import { baseConnectionUrl, makeUrl } from "../connection";

import { components } from "../schema";

type ShipmentMethodModel = components["schemas"]["ShipmentMethodModel"];
type CurrencyModel = components["schemas"]["CurrencyModel"];
type CountryModel = components["schemas"]["CountryModel"];
type LabelPrintModel = components["schemas"]["LabelPrintModel"];

export const useQueryShipmentMethods = () => {
  const { data } = useQuery({
    queryKey: ["methodsCodelist"],
    queryFn: () => {

      const defs = makeUrl("methods");
      return fetch(`${defs}`).then(x => x.json() as Promise<ShipmentMethodModel[]>);
    },
  });

  return data;
};

export const useQueryCurrencies = () => {
  const { data } = useQuery({
    queryKey: ["currencies"],
    queryFn: () => {
      const defs = makeUrl("currencies");
      return fetch(`${defs}`).then(x => x.json() as Promise<CurrencyModel[]>);
    },
  });

  return data;
};

export const useQueryCountries = () => {
  const { data } = useQuery({
    queryKey: ["countries"],
    queryFn: () => {
      const defs = makeUrl("countries");
      return fetch(`${defs}`).then(x => x.json() as Promise<CountryModel[]>);
    },
  });

  return data;
};

export const useQueryLabelPrint = () =>
  useQuery({
    queryKey: ["print-setting-printers"],
    queryFn: () => {
      const defs = makeUrl("availablePrinters");
      return fetch(`${defs}`).then(x => x.json() as Promise<LabelPrintModel[]>);
    },
  });

export const useQueryPayments = () => {
  const { data } = useQuery({
    queryKey: ["payments"],
    queryFn: () => {
      const defs = makeUrl("payments");
      return fetch(`${defs}`).then(x => x.json() as Promise<string[]>);
    },
  });
  return data;
};
