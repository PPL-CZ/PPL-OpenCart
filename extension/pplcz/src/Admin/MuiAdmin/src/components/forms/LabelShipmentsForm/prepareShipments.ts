import { UseFormSetError, UseFormSetValue } from "react-hook-form";
import { makeUrl } from "../../../connection";
import { ValidationError } from "../../../queries/types";
import { components } from "../../../schema";

type PrepareShipmentBatchItemModel = components["schemas"]["PrepareShipmentBatchItemModel"];
type PrepareShipmentBatchReturnModel = components["schemas"]["PrepareShipmentBatchReturnModel"];
type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

type CreteLabelShipmentItems = {
  labelPrintSetting: string;
  items: ShipmentWithAdditionalModel[];
};

export const prepareShipments = async (
  batchId: string,
  values: PrepareShipmentBatchItemModel[],
  controller: AbortController,
  setError: UseFormSetError<CreteLabelShipmentItems>,
  setValue: UseFormSetValue<CreteLabelShipmentItems>
) => {
  const url = makeUrl("prepareBatchLabelShipments", {
    batch_id: batchId,
  });

  const response = await fetch(url, {
    headers: {
      "Content-Type": "application/json",
    },
    signal: controller.signal,
    method: "POST",
    body: JSON.stringify({ items: values }),
  });

  if (response.status === 200) {
    const ret = (await response.json()) as PrepareShipmentBatchReturnModel;
    ret.shipmentId?.forEach((x, index) => {
      setValue(`items.${index}.shipment.id`, x);
    });
    return true;
  } else if (response.status === 400) {
    const validationError = (await response.json()).data as ValidationError;
    Object.keys(validationError.errors).forEach(key => {
      const error = validationError.errors[key];
      const validKey = key.replace(/^item\.([0-9]+)/, item => {
        const num = item.match(/[0-9]+/)!;
        return `items.${parseInt(num[0])}`;
      });

      // @ts-ignore
      setError(validKey, {
        type: "server",
        message: error[0],
      });
    });
    return false;
  }

  throw new Error("Problém s přípravou zásilek");
};

export default prepareShipments;
