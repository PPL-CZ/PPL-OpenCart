import { Fragment, useCallback, useEffect, useRef, useState } from "react";
import { components } from "../../../schema";
import IconButton from "@mui/material/IconButton";
import TableCell from "@mui/material/TableCell";
import TableRow from "@mui/material/TableRow";
import { useFormContext, useFormState } from "react-hook-form";
import WindowIcon from "@mui/icons-material/OpenInBrowser";
import { useTableStyle } from "./styles";
import { ArrowDownward, ArrowUpward, DragHandle } from "@mui/icons-material";
import {
  useRefreshBatch,
  useRemoveShipmentFromBatch,
  useReorderShipmentInBatch,
} from "../../../queries/useBatchQueries";
import MoreVert from "@mui/icons-material/MoreVert";
import Menu from "@mui/material/Menu";
import List from "@mui/material/List";
import ListItemButton from "@mui/material/ListItemButton";
import CreateShipmentWidget from "../../widgets/CreateShipmentWidget";
import Package from "./package";
import { makeOrderUrl } from "../../../connection";
import { useDrag, useDrop } from "react-dnd";
type ShipmentWithAdditionalModel = components["schemas"]["ShipmentWithAdditionalModel"];

const Item = (props: {
  batchId: string;
  maxRows: number;
  position: number;
  flashId: string | null;
  hideOrderAnchor?: boolean;
  setFlashId: (flashId: string) => void;
  swap: (from: number, to: number) => void;
}) => {
  const {
    classes: { trError },
  } = useTableStyle();

  const reorderShipmentInBatch = useReorderShipmentInBatch(props.batchId);
  const removeShipmentFromBatch = useRemoveShipmentFromBatch(props.batchId);
  const refreshBatch = useRefreshBatch(props.batchId);

  const [showMenu, setShowMenu] = useState(false);
  const [edit, setEdit] = useState(0);
  const [anchorEl, setAnchorEl] = useState<HTMLElement | null>(null);

  const { watch, getValues } = useFormContext<{ items: ShipmentWithAdditionalModel[] }>();
  const { errors } = useFormState();

  let className = "";

  // @ts-ignore
  if (errors.items?.[props.position]) {
    className = trError;
  }

  const model = watch(`items.${props.position}`);

  const basicData = model;
  const recipient = model.shipment.recipient;
  const parcel = model.shipment.parcel;
  let cod = false;

  const packages = (() => {
    const packages = model.shipment.packages;
    if (!packages?.[0]?.shipmentNumber) {
      return packages?.length ?? 0;
    }
    return packages.map((x, index) => (
      <Package key={x.id!} index={index} batchId={props.batchId} shipmentId={`${model.shipment.id}`} package={x} />
    ));
  })();

  const newPosition = useCallback(
    (newPosition: number, finish: boolean) => {
      props.swap(props.position, newPosition);
      const rows = getValues("items");
      if (!finish) {
        const id = rows[props.position]?.shipment.id;
        props.setFlashId(`${id}`);
      } else {
        const sub = newPosition - 1;
        const up = newPosition + 1;

        let batchRowSub = 0;
        let batchRowUp = rows[rows.length - 1].shipment.batchOrder || 100;

        if (rows[sub]) batchRowSub = rows[sub].shipment.batchOrder || 0;
        if (rows[up]) batchRowUp = rows[up].shipment.batchOrder || 0;

        if (newPosition === rows.length - 1) {
          batchRowUp = batchRowSub + 100;
        }

        const batchOrder = (batchRowUp - batchRowSub) / 2 + batchRowSub;
        reorderShipmentInBatch.mutateAsync({
          shipment_id: `${model.shipment.id}`,
          position: batchOrder,
        });
      }
    },
    [props.position]
  );

  const handleRef = useRef<HTMLButtonElement | null>(null);

  const positions = (() => {
    const items = [
      <IconButton
        key={"up"}
        onClick={e => {
          e.preventDefault();
          newPosition(props.position - 1, true);
        }}
      >
        <ArrowUpward />
      </IconButton>,
      <IconButton
        key="down"
        onClick={e => {
          e.preventDefault();
          newPosition(props.position + 1, true);
        }}
      >
        <ArrowDownward />
      </IconButton>,
    ];
    if (props.position === 0) items.shift();
    if (props.position === props.maxRows - 1) items.pop();
    items.push(
      <IconButton ref={handleRef}>
        <DragHandle fontSize="small" />
      </IconButton>
    );
    return <>{items}</>;
  })();

  const DND_TYPE = "ROW";

  const ref = useRef<HTMLTableRowElement | null>(null);

  const [{ isDragging }, drag] = useDrag(
    () => ({
      type: DND_TYPE,
      item: { index: props.position },
      collect: monitor => ({ isDragging: monitor.isDragging() }),
    }),
    [props.position]
  );

  const [, drop] = useDrop(
    () => ({
      accept: DND_TYPE,
      hover: (item: { index: number }, monitor) => {
        if (!ref.current) return;
        const dragIndex = item.index;
        const hoverIndex = props.position;
        if (dragIndex === hoverIndex) return;

        const hoverBoundingRect = ref.current.getBoundingClientRect();
        const hoverMiddleY = (hoverBoundingRect.bottom - hoverBoundingRect.top) / 2;
        const clientOffset = monitor.getClientOffset();
        if (!clientOffset) return;
        const hoverClientY = clientOffset.y - hoverBoundingRect.top;

        if (dragIndex < hoverIndex && hoverClientY < hoverMiddleY) return;
        if (dragIndex > hoverIndex && hoverClientY > hoverMiddleY) return;

        if (props.position === dragIndex) return;

        newPosition(dragIndex, false);
        item.index = hoverIndex;
      },
      drop: (item, monitor) => {
        if (!monitor.didDrop()) {
          newPosition(item.index, true);
        }
      },
    }),
    [newPosition]
  );

  drop(ref);
  drag(handleRef);

  return (
    <TableRow
      ref={ref}
      className={className}
      sx={{
        transition: "background-color 0.8s ease",
        backgroundColor: props.flashId === `${model.shipment.id}` ? "rgba(255, 235, 59, 0.3)" : undefined,
      }}
    >
      <TableCell>
        {edit ? (
          <CreateShipmentWidget
            shipment={model.shipment}
            onFinish={() => {
              setEdit(0);
              refreshBatch();
            }}
          />
        ) : null}
        {basicData.shipment.orderId}{" "}
        {model.shipment.orderId && props.hideOrderAnchor !== false ? (
          <IconButton
            onClick={e => {
              e.preventDefault();
              const url = makeOrderUrl(`${model.shipment.orderId}`);
              window.open(url);
            }}
          >
            <WindowIcon />
          </IconButton>
        ) : null}{" "}
        {basicData.shipment.id ? `(${basicData.shipment.id})` : null}
      </TableCell>
      <TableCell>
        {basicData.shipment.hasParcel
          ? (() => {
              const parcelAddress = [
                (parcel?.name || "") + " " + (parcel?.name2 || ""),
                parcel?.street,
                (parcel?.zip || "") + " " + (parcel?.city + ""),
              ]
                .filter(x => x && x.trim())
                .map((x, index) => {
                  return (
                    <Fragment key={index}>
                      {x}
                      <br />
                    </Fragment>
                  );
                });
              if (parcelAddress) return <address>{parcelAddress}</address>;
              return null;
            })()
          : (() => {
              const recepientAddress = [
                recipient?.name,
                recipient?.contact,
                recipient?.street,
                (recipient?.zip || "") + " " + (recipient?.city + ""),
              ]
                .filter(x => x && x.trim())
                .map((x, index) => {
                  return (
                    <Fragment key={index}>
                      {x}
                      <br />
                    </Fragment>
                  );
                });
              if (recepientAddress.length) return <address>{recepientAddress}</address>;
              return null;
            })()}
      </TableCell>
      <TableCell>{basicData.shipment.serviceName}</TableCell>
      <TableCell>{packages}</TableCell>
      <TableCell>
        {!cod && basicData.shipment.codValue
          ? basicData.shipment.codValue + (basicData.shipment.codValueCurrency || "")
          : ""}
      </TableCell>
      <TableCell>{basicData.shipment.codVariableNumber || ""}</TableCell>
      {model.shipment.lock ? null : <TableCell>{positions}</TableCell>}
      {model.shipment.lock ? null : (
        <TableCell>
          <>
            <IconButton
              id={`${props.position}`}
              onClick={e => {
                e.stopPropagation();
                setAnchorEl(e.currentTarget);
                setShowMenu(true);
              }}
            >
              <MoreVert />
            </IconButton>
            <div>
              <Menu
                anchorEl={anchorEl}
                id="popover"
                className="wp-reset-div"
                open={showMenu}
                onClose={() => {
                  setShowMenu(false);
                }}
              >
                <List component="nav">
                  {!model.shipment.lock ? (
                    <ListItemButton
                      onClick={e => {
                        removeShipmentFromBatch.mutateAsync({
                          shipment_id: model.shipment.id!,
                        });
                      }}
                    >
                      Vyřadit z tisku
                    </ListItemButton>
                  ) : null}
                  <ListItemButton
                    onClick={e => {
                      setEdit(model.shipment.id!);
                      setShowMenu(false);
                    }}
                  >
                    Detail
                  </ListItemButton>
                </List>
              </Menu>
            </div>
          </>
        </TableCell>
      )}
    </TableRow>
  );
};

export default Item;
