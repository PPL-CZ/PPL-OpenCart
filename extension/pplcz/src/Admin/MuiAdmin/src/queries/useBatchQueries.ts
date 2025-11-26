import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { baseConnectionUrl, makeUrl } from "../connection";

import { components } from "../schema";
import { UnknownErrorException, ValidationErrorException } from "./types";

type BatchModel = components["schemas"]["BatchModel"];
type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

export const useBatch = () => {
  return useQuery({
    queryKey: ["batchs"],
    retry: (count, error) => {
      return count < 3;
    },
    queryFn: async () => {
      const baseUrl = makeUrl("getBatches");
      const data = await fetch(baseUrl, {}).then(x => x.json());

      return data as BatchModel[];
    },
  });
};

export const useBatchShipment = (batchId: string) => {
  return useQuery({
    queryKey: ["batchs-" + batchId],
    retry: (count, error) => {
      return count < 3;
    },
    queryFn: async () => {
      const baseUrl = makeUrl("getBatchShipments", {
        batch_id: batchId,
      });
      const data = await fetch(baseUrl).then(x => x.json());
      return data as ShipmentWithAdditionalModel[];
    },
  });
};

export const useRemoveShipmentFromBatch = (batchId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationKey: ["batchs-remove-" + batchId],
    mutationFn: async (variables: { shipment_id: number }) => {
      const baseUrl = makeUrl("removeBatchShipment", variables);
      await fetch(baseUrl, {
        method: "DELETE",
      });
    },
    onSuccess: (data, error, context) => {
      queryClient.invalidateQueries({ queryKey: ["batchs-" + batchId] });
    },
  });
};

export const useRefreshBatch = (batchId: string) => {
  const queryClient = useQueryClient();
  return () => queryClient.invalidateQueries({ queryKey: ["batchs-" + batchId] });
};

export const useReorderShipmentInBatch = (batchId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationKey: ["batchs-reorder-" + batchId],
    mutationFn: async (variables: { shipment_id: string; position: number }) => {
      const baseUrl = makeUrl("reorderShipment", variables);
      await fetch(baseUrl, {
        method: "PUT",
      });
    },
    onSuccess: (data, error, context) => {
      queryClient.invalidateQueries({ queryKey: ["batchs-" + batchId] });
    },
  });
};

export const useCancelShipment = (batchId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationKey: ["batchs-cancel-" + batchId],
    mutationFn: async (variables: { shipment_id: number }) => {
      const baseUrl = makeUrl("cancelBatchShipment", variables);
      await fetch(baseUrl, {
        method: "PUT",
      });
    },
    onSuccess: (data, error, context) => {
      queryClient.invalidateQueries({ queryKey: ["batchs-" + batchId] });
    },
  });
};

export const useTestState = (batchId: string) => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationKey: ["batchs-test-" + batchId],
    mutationFn: async (variables: { shipment_id: number }) => {
      const baseUrl = makeUrl("testBatchShipment", variables);
      await fetch(baseUrl, {
        method: "PUT",
      });
    },
    onSuccess: (data, error, context) => {
      queryClient.invalidateQueries({ queryKey: ["batchs-" + batchId] });
    },
  });
};
