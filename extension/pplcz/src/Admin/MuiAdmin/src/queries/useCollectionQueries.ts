import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { baseConnectionUrl, makeUrl } from "../connection";

import { components } from "../schema";
import { UnknownErrorException, ValidationErrorException } from "./types";

type CollectionModel = components["schemas"]["CollectionModel"];
type NewCollectionModel = components["schemas"]["NewCollectionModel"];
type CollectionAddressModel = components["schemas"]["CollectionAddressModel"];
export const useCollections = () => {
  return useQuery({
    queryKey: ["collections"],
    retry: (count, error) => {
      return count < 3;
    },
    queryFn: async () => {
      const url = makeUrl("getCollections");
      const data = await fetch(url).then(x => x.json());

      return data as CollectionModel[];
    },
  });
};

export const useAddressCollection = () => {
  return useQuery({
    queryKey: ["addressCollection"],
    queryFn: async () => {
      const url = makeUrl("getCollectionAddress");
      const data = await fetch(url).then(x => (x.status === 200 ? x.json() : null));

      return (data as CollectionAddressModel) || null;
    },
  });
};

export const useLastCollection = () => {
  return useQuery({
    queryKey: ["lastCollection"],
    queryFn: async () => {
      const url = makeUrl("getLastCollection");
      const data = await fetch(url).then(x => x.json());

      return data as CollectionModel;
    },
  });
};

export const useNewCollection = () => {
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (data, error, context) => {
      queryClient.refetchQueries({
        queryKey: ["lastCollection"],
      });
      queryClient.refetchQueries({
        queryKey: ["collections"],
      });
    },
    mutationFn: async (collection: NewCollectionModel) => {
      const baseUrl = makeUrl("createCollection");

      return await fetch(baseUrl, {
        method: "POST",
        headers: {
          "content-type": "application/json",
        },
        body: JSON.stringify(collection),
      })
        .then(async x => {
          if (x.status === 400) {
            const data = await x.json();
            throw new ValidationErrorException(x.status, data.data);
          } else if (x.status > 400) throw new UnknownErrorException(x.status);
          return x;
        })
        .then(x => x.headers.get("location"))!;
    },
  });
};
