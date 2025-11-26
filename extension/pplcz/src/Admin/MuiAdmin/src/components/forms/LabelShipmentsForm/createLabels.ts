import { UseFormSetError, UseFormSetValue } from "react-hook-form";
import { baseConnectionUrl, makeUrl } from "../../../connection";
import { components } from "../../../schema";

type ShipmentModel = components["schemas"]["ShipmentModel"];
type CreateShipmentLabelBatchModel = components["schemas"]["CreateShipmentLabelBatchModel"];
type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

type CreteLabelShipmentItems = {
  labelPrintSetting: string;
  items: ShipmentWithAdditionalModel[];
};

const createLabels = async (
  batchId: string,
  shipmentId: number[],
  printSetting: string,
  controller: AbortController,
  setError: UseFormSetError<CreteLabelShipmentItems>,
  setValue: UseFormSetValue<CreteLabelShipmentItems>
) => {
  const url = makeUrl("createBatchLabelShipments", {
    batch_id: batchId,
  });

  const response = await fetch(url, {
    headers: {
      "Content-Type": "application/json",
    },
    signal: controller.signal,
    method: "POST",
    body: JSON.stringify({ printSetting, shipmentId } as CreateShipmentLabelBatchModel),
  });

  if (response.status === 200 || response.status === 400) {
    const ret = (await response.json()) as ShipmentModel[];
    for (let i = 0; i < ret.length; i++) {
      // @ts-ignore
      setValue(`item.${i}`, ret[i]);
      if (response.status === 400) {
        // @ts-ignore
        setError(`items.${i}`, { message: "Chyba při importu" });
      }
    }

    if (response.status === 400) {
      return false;
    } else {
      return true;
    }
  }

  if (response.status === 204) return true;

  throw new Error("Problém s přípravou zásilek");
};

export default createLabels;
