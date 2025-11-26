import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { components } from "../schema";
import { baseConnectionUrl, makeUrl } from "../connection";
import { UnknownErrorException, ValidationErrorException } from "./types";

type SenderAddressModel = components["schemas"]["SenderAddressModel"];
type SyncPhasesModel = components["schemas"]["SyncPhasesModel"];
type ShopModel = components["schemas"]["ShopModel"];
type ShipmentMethodSettingModel = components["schemas"]["ShipmentMethodSettingModel"];

export const useSenderAddressesQuery = (storeId = 0) => {
  const { data } = useQuery({
    queryKey: [`sender-addresses-${storeId}`],
    queryFn: () => {
      const defs = makeUrl("senderAddresses", { store_id: storeId });
      return fetch(`${defs}`).then(x => x.json() as Promise<SenderAddressModel[]>);
    },
  });
  return data;
};

export const useSenderAddressesMutation = (storeId = 0) => {
  const qc = useQueryClient();
  return useMutation({
    mutationKey: [`sender-addresses-${storeId}`],
    mutationFn: (data: SenderAddressModel[]) => {
      const defs = makeUrl("senderAddressesEdit", { store_id: storeId });
      return fetch(defs, {
        method: "PUT",
        headers: {
          "content-type": "application/json",
        },
        body: JSON.stringify(data),
      }).then(async x => {
        if (x.status === 400) {
          const data = await x.json();
          throw new ValidationErrorException(x.status, data.data);
        } else if (x.status > 400) throw new UnknownErrorException(x.status);
        return x;
      });
    },
    onSuccess: () => {
      qc.refetchQueries({
        queryKey: [`sender-addresses-${storeId}`],
      });
    },
  });
};

export const useLabelPrintSettingQuery = () => {
  return useQuery({
    queryKey: ["print-setting"],
    queryFn: async () => {
      const defs = makeUrl("print");
      return fetch(defs).then(x => x.json() as Promise<string>);
    },
  });
};

export const useLabelPrintSettingMutation = () => {
  const qc = useQueryClient();
  return useMutation({
    mutationKey: ["print-setting"],
    mutationFn: async (data: { printState: string }) => {
      const url = makeUrl("printEdit");
      return fetch(url, {
        method: "POST",
        headers: {
          "content-type": "application/json",
        },
        body: JSON.stringify({
          "format" : data.printState
        }),
      });
    },
    onSuccess: () => {
      qc.refetchQueries({
        queryKey: [`print-setting`],
      });
    },
  })
}

export const useQueryShipmentStates = () =>
  useQuery({
    queryKey: ["phase-shipments"],
    queryFn: () => {
      const url = makeUrl("phases");
      return fetch(url, {
        method: "GET",
      }).then(x => x.json() as Promise<SyncPhasesModel>);
    },
  });

export const useShopsQuery = () =>
  useQuery({
    queryKey: ["shops"],
    queryFn: () => {
      const url = makeUrl("shops");
      return fetch(url, {
        method: "GET",
      }).then(x => x.json() as Promise<ShopModel[]>);
    },
  });

export const useShipmentSettings = (id: number = 0) => {
  const result = useQuery({
    queryKey: [`shipment-setting-${id}`],
    queryFn: () => {
      const url = makeUrl("shipmentSettings", { store_id: id });
      return fetch(url).then(x => x.json() as Promise<ShipmentMethodSettingModel[]>);
    },
  });
  return result.data;
};

export const useShipmentSettigMutation = (storeId = 0) => {
  const qc = useQueryClient();
  return useMutation({
    mutationKey: [`shipment-setting-${storeId}`],
    mutationFn: (data: ShipmentMethodSettingModel) => {
      const defs = makeUrl("shipmentSettingEdit", { store_id: storeId });
      return fetch(defs, {
        method: "PUT",
        headers: {
          "content-type": "application/json",
        },
        body: JSON.stringify(data),
      }).then(async x => {
        if (x.status === 400) {
          const data = await x.json();
          throw new ValidationErrorException(x.status, data.data);
        } else if (x.status > 400) throw new UnknownErrorException(x.status);
        return x;
      });
    },
    onSuccess: () => {
      qc.refetchQueries({
        queryKey: [`shipment-setting-${storeId}`],
      });
    },
  });
};

export const useShipmentSettigRemovingMutation = (storeId = 0) => {
  const qc = useQueryClient();
  return useMutation({
    mutationKey: [`shipment-setting-${storeId}`],
    mutationFn: (data: ShipmentMethodSettingModel) => {
      const defs = makeUrl("shipmentSettingDelete", { store_id: storeId, guid: data.guid });
      return fetch(defs, {
        method: "DELETE",
        headers: {
          "content-type": "application/json",
        },
      }).then(async x => {
        if (x.status === 400) {
          const data = await x.json();
          throw new ValidationErrorException(x.status, data.data);
        } else if (x.status > 400) throw new UnknownErrorException(x.status);
        return x;
      });
    },
    onSuccess: () => {
      qc.refetchQueries({
        queryKey: [`shipment-setting-${storeId}`],
      });
    },
  });
};
